<?php

namespace AgenterLab\Database;


trait SoftDeletes
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    
    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new SoftDeletingScope);
    }

    /**
     * Determine if the model instance has been soft-deleted.
     *
     * @return bool
     */
    public function trashed()
    {
        return ! empty($this->{$this->getIsDeletedColumn()});
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore()
    {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getIsDeletedColumn()} = 0;


        $this->{$this->getDeletedAtColumn()} = 0;

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function runSoftDelete()
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        $time = $this->freshTimestamp();

        $columns = [$this->getDeletedAtColumn() => $this->fromDateTime($time), $this->getIsDeletedColumn() => 1];

        $this->{$this->getDeletedAtColumn()} = $time;

        $this->{$this->getIsDeletedColumn()} = 1;

        if ($this->timestamps && ! empty($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));
    }



    /**
     * Get the name of the "is deleted" column.
     *
     * @return string
     */
    public function getIsDeletedColumn()
    {
        return defined('static::IS_DELETED') ? static::IS_DELETED : 'is_deleted';
    }

    /**
     * Get the fully qualified "is deleted" column.
     *
     * @return string
     */
    public function getQualifiedIsDeletedColumn()
    {
        return $this->qualifyColumn($this->getIsDeletedColumn());
    }
}