<?php 

namespace Stevebauman\Maintenance\Controllers;

use Stevebauman\Maintenance\Validators\WorkOrderPartPutBackValidator;
use Stevebauman\Maintenance\Validators\WorkOrderPartTakeValidator;
use Stevebauman\Maintenance\Services\InventoryStockMovementService;
use Stevebauman\Maintenance\Services\InventoryStockService;
use Stevebauman\Maintenance\Services\InventoryService;
use Stevebauman\Maintenance\Services\WorkOrderService;
use Stevebauman\Maintenance\Controllers\AbstractController;

class WorkOrderPartStockController extends AbstractController {
    
    public function __construct(
            WorkOrderService $workOrder, 
            InventoryService $inventory, 
            InventoryStockService $inventoryStock,
            InventoryStockMovementService $inventoryStockMovement,
            WorkOrderPartTakeValidator $workOrderPartTakeValidator,
            WorkOrderPartPutBackValidator $workOrderPartPutBackValidator){
        $this->workOrder = $workOrder;
        $this->inventory = $inventory;
        $this->inventoryStock = $inventoryStock;
        $this->inventoryStockMovement = $inventoryStockMovement;
        $this->workOrderPartTakeValidator = $workOrderPartTakeValidator;
        $this->workOrderPartPutBackValidator = $workOrderPartPutBackValidator;
    }
    
    /**
     * Display Inventory item stocks per location available to transfer into the
     * work order.
     * 
     * @param type $workOrder_id
     * @param type $inventory_id
     * @return type Response
     */
    public function index($workOrder_id, $inventory_id){
        $workOrder = $this->workOrder->find($workOrder_id);
        $item = $this->inventory->find($inventory_id);
        
        return $this->view('maintenance::work-orders.parts.stocks.index', array(
            'title' => 'Choose a Stock Location',
            'workOrder' => $workOrder,
            'item' => $item
        ));
    }
    
    /**
     * Display the form to update the quantity the user is taking from the inventory
     * for the work order.
     * 
     * @param type $workOrder_id
     * @param type $inventory_id
     * @param type $stock_id
     * @return type Response
     */
    public function create($workOrder_id, $inventory_id, $stock_id){
        
        $workOrder = $this->workOrder->find($workOrder_id);
        $item = $this->inventory->find($inventory_id);
        $stock = $this->inventoryStock->find($stock_id);
        
        return $this->view('maintenance::work-orders.parts.stocks.create', array(
            'title' => "Enter Quantity Used",
            'workOrder' => $workOrder,
            'item' => $item,
            'stock' => $stock
        ));
        
    }
    
    /**
     * Process the quantity the user is taking from the stock location
     * 
     * @param type $workOrder_id
     * @param type $inventory_id
     * @param type $stock_id
     * @return type Response
     */
    public function store($workOrder_id, $inventory_id, $stock_id){
        
        if($this->workOrderPartTakeValidator->passes()){
            
            $workOrder = $this->workOrder->find($workOrder_id);
            $item = $this->inventory->find($inventory_id);
            $stock = $this->inventoryStock->find($stock_id);
            
            /*
             * Grab all input data
             */
            $data = $this->inputAll();

            /*
             * Add Part to work order (passing in the work order and the stock)
             */
            $this->workOrder->setInput($data)->savePart($workOrder, $stock);
 
            /*
             * Set the extra input data for the inventory stock change reason
             */
            $data['reason'] = sprintf('Used for <a href="%s">Work Order</a>', route('maintenance.work-orders.show', array($workOrder->id)));

            /*
             * Perform a take from the stock
             */
            $this->inventoryStock->setInput($data)->take($stock->id);
            
            /*
             * Set the return messages
             */
            $this->message = sprintf(
                    'Successfully added %s of %s to work order. %s or %s', 
                        $this->input('quantity'), 
                        $item->name, 
                        link_to_route('maintenance.work-orders.parts.index', 'Add More', array($workOrder->id)),
                        link_to_route('maintenance.work-orders.show', 'View Work Order', array($workOrder->id))
                    );
            
            $this->messageType = 'success';
            $this->redirect = route('maintenance.work-orders.parts.index', array($workOrder->id));
            
        } else{
            
            $this->errors = $this->workOrderPartTakeValidator->getErrors();
            $this->redirect = route('maintenance.work-orders.parts.stocks.create', array(
                $workOrder_id, $inventory_id, $stock_id
            ));
        }
        
        return $this->response();
    }
    
    /**
     * Destroys the pivot table entry of the stock quantity used in the work order,
     * then returns the quantity back to the stock and creates a movement indicating
     * that the quantity of the item was put back from a work order.
     * 
     * @param type $workOrder_id
     * @param type $inventory_id
     * @param type $stock_id
     * @return type Response
     */
    public function postPutBack($workOrder_id, $inventory_id, $stock_id){
        
        $workOrder = $this->workOrder->find($workOrder_id);
        $item = $this->inventory->find($inventory_id);
        $stock = $this->inventoryStock->find($stock_id);
        
        /*
         * Find the specific work order part record from the stock id
         */
        $record = $workOrder->parts->find($stock->id);
        
        /*
         * Set the reason and quantity of why the putting back is taking place
         */
        $data = array(
            'reason' => sprintf('Put back from <a href="%s">Work Order</a>', route('maintenance.work-orders.show', array($workOrder->id))),
            'quantity' => $record->pivot->quantity
        );
        
        /*
         * Update the inventory stock record
         */
        $this->inventoryStock->setInput($data)->put($stock->id);
        
        /*
         * Remove the part from the work order
         */
        $workOrder->parts()->detach($stock->id);
        
        $this->message = sprintf('Successfully put back %s into the inventory', $item->name);
        $this->messageType = 'success';
        $this->redirect = route('maintenance.work-orders.show', array($workOrder->id));
        
        return $this->response();
    }
    
    public function postPutBackSome($workOrder_id, $inventory_id, $stock_id)
    {
        $workOrder = $this->workOrder->find($workOrder_id);
        $item = $this->inventory->find($inventory_id);
        $stock = $this->inventoryStock->find($stock_id);
        
        $this->workOrderPartPutBackValidator->addRule('quantity', 'less_than:'.$stock->quantity);
        
        if($this->workOrderPartPutBackValidator->passes()){

            /*
             * Find the specific work order part record from the stock id
             */
            $record = $workOrder->parts->find($stock->id);

            /*
             * Set the reason and quantity of why the putting back is taking place
             */
            $data = array(
                'reason' => sprintf('Put back from <a href="%s">Work Order</a>', route('maintenance.work-orders.show', array($workOrder->id))),
                'quantity' => $this->input('quantity')
            );

            /*
             * Update the inventory stock record
             */
            $this->inventoryStock->setInput($data)->put($stock->id);

            /*
             * Set the new pivot quantity
             */
            $newQuantity = $record->pivot->quantity - $this->input('quantity');

            /*
             * Updat the existing pivot record
             */
            $workOrder->parts()->updateExistingPivot($stock->id, array('quantity'=>$newQuantity));

            $this->message = sprintf('Successfully put back %s into the inventory', $item->name);
            $this->messageType = 'success';
            $this->redirect = route('maintenance.work-orders.show', array($workOrder->id));

        } else {

            $this->errors = $this->workOrderPartPutBackValidator->getErrors();
            $this->redirect = route('maintenance.work-orders.parts.stocks.index', array($workOrder_id, $inventory_id));

        }
        
        return $this->response();
    }
    
}