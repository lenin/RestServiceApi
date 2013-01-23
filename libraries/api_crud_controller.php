<?php
/**
 * Author: LeninYee
 * Date: 13-01-22
 * File: api_crud_controller.php
 * Email: leninyee@gmail.com
 */
abstract class Api_Crud_Controller extends Api_Controller
{
	public static $model_class;
	public static $rules;

	public function __construct()
	{
		$this->filter('before', 'require_model_class', array(static::$model_class));
	}

	public function find_object_or_404($id)
	{
		$obj = call_user_func(array(static::$model_class, 'find'), $id);
		if(is_null($obj)) {
            return Response::json(static::$model_class . ' not found', 404);
        }
        return $obj;
	}

    public function get_index($id = null)
	{
		if (is_null($id)) {
			return Response::eloquent(call_user_func(static::$model_class . '::all'));
		}

		$obj = $this->find_object_or_404($id);
		if(!is_a($obj, static::$model_class)) {
            return $obj;
        }

        return Response::eloquent($obj);
	}

	public function post_index()
    {
        $params = Api::parameters_check(isset(static::$rules['create']) ?: null);
        if (is_string($params)) {
            return Api::parameters_error($params);
        }

        $obj = call_user_func(static::$model_class . '::create', $params);
 
        return Response::eloquent($obj);
    }

    public function put_index($id)
    {
        $params = Api::parameters_check(isset(static::$rules['update']) ?: null);
        if (is_string($params)) {
            return Api::parameters_error($params);
        }

        $obj = $this->find_object_or_404($id);
		if(!is_a($obj, static::$model_class)) {
            return $obj;
        }

        foreach ($params as $field => $value) {
        	$obj->$field = $value;
        }
        $obj->save();
 
        return Response::eloquent($obj);
    }

    public function delete_index($id)
    {
        $obj = $this->find_object_or_404($id);
		if(!is_a($obj, static::$model_class)) {
            return $obj;
        }

        $deleted_obj = $obj;
        $obj->delete();
 
        return Response::eloquent($deleted_obj);
    }
}