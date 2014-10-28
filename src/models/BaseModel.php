<?php namespace Stevebauman\Maintenance\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model as Eloquent;

class BaseModel extends Eloquent {
    
    /*
     * Revisionable Trait for storing revisions on all models that extend
     * from this class
     * 
     */
    use \Venturecraft\Revisionable\RevisionableTrait;
    
    /**
     * Formats the created_at timestamp
     * 
     * @param string $created_at
     * @return string
     */
    public function getCreatedAtAttribute($created_at){
        return Carbon::parse($created_at)->format('M dS Y - h:ia'); 
    }
    
    /**
     * Formats the deleted_at timestamp
     * 
     * @param string $deleted_at
     * @return string
     */
    public function getDeletedAtAttribute($deleted_at){
        return Carbon::parse($deleted_at)->format('M dS Y - h:ia'); 
    }
    
    /**
     * 
     * 
     * @param string $string
     * @return boolean OR array
     */
    protected function getOperator($string){
        $allowed_operators = array('>', '<', '=', '>=', '<=');
        $output = preg_split("/[\[\]]/", $string);

        if(is_array($output)){
                if(array_key_exists('1', $output) && array_key_exists('2', $output)){
                        if(in_array($output[1], $allowed_operators)){
                                return array($output[1], $output[2]);
                        }
                } else{
                    return $output;
                }
        }
        return false;
    }
    
    /**
     * Allows all tables extending from the base model to be scoped by ID
     * 
     * @param object $query
     * @param integer/string $id
     * @return object
     */
    public function scopeId($query, $id = NULL){
        if($id){
            return $query->where('id', $id);
        }
    }
    
    /**
     * Allows all columns on the current database table to be sorted through
     * query scope
     * 
     * @param object $query
     * @param string $field
     * @param string $sort
     * @return object
     */
    public function scopeSort($query, $field = NULL, $sort = NULL){
        
        /*
         * Make sure both the field and sort variables are present
         */
        if($field && $sort){
            /*
             * Retrieve all column names for the current model table
             */
            $columns = Schema::getColumnListing($this->table);

            /*
             * Make sure the field inputted is available on the current table
             */
            if(in_array($field, $columns)){

                /*
                 * Make sure the sort input is equal to asc or desc
                 */
                if($sort === 'asc' || $sort === 'desc'){
                    /*
                     * Return the query sorted
                     */
                    return $query->orderBy($field, $sort);
                }
            }
        }
        
        /*
         * Default order by created at field
         */
        return $query->orderBy('created_at', 'desc');
        
    }
    
    public function scopeArchived($query, $archived = NULL)
    {
        if($archived){
            return $query->onlyTrashed();
        }
    }
    
    /**
     * Allows all models extending from BaseModel to have notifications
     * 
     * @return object
     */
    public function notifications(){
        return $this->morphMany('Stevebauman\Maintenance\Models\Notification', 'notifiable');
    }
}