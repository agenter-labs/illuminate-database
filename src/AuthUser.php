<?php

namespace AgenterLab\Database;

trait AuthUser
{

    /**
     * Indicates if the model should be authenticated.
     *
     * @var bool
     */
    public $authUser = true;

    /**
     * The name of the "created by" column.
     *
     * @var string|null
     */
    public $created_by_name = 'created_by';

    /**
     * The name of the "updated by" column.
     *
     * @var string|null
     */
    public $updated_by_name = 'updated_by';
    
    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootAuthUser()
    {
        static::creating(function ($model) {
            $model->updateAuthUser();
        });

        static::updating(function ($model) {
            $model->updateAuthUser();
        });
    }

    /**
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesAuthUser()
    {
        return $this->authUser;
    }

    /**
     * Set Auth user
     */
    protected function updateAuthUser() {

        if (!$this->usesAuthUser()) {
            return true;  
        }

        $authId = auth()->id();

        if ($authId) {

            if (! is_null($this->updated_by_name) && ! $this->isDirty($this->updated_by_name)) {
                $this->setUpdatedBy($authId);
            }
    
            if (! $this->exists && ! is_null($this->created_by_name) && ! $this->isDirty($this->created_by_name)) {
                $this->setCreatedBy($authId);
            }
        }
    }


    /**
     * Set the value of the "created at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setCreatedBy($value)
    {
        $this->{$this->created_by_name} = $value;

        return $this;
    }

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setUpdatedBy($value)
    {
        $this->{$this->updated_by_name} = $value;

        return $this;
    }
}