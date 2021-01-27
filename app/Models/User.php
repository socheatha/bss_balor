<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Setting;
use Route;
use Auth;

class User extends Authenticatable
{
	use Notifiable, HasRoles;

	protected $fillable = [
		'first_name', 'last_name', 'phone', 'gender', 'image', 'language', 'status', 'approval', 'email', 'password', 'position',
	];

	protected $hidden = [
		'password', 'remember_token',
	];

	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	public function isApproved($user){

		if ($user->approval == 1) {
			return true;
		}
		
		return false;
	}


	public function setting(){
		$setting = Setting::find(1);
		return $setting;
	}


	
	public function sidebarActive(){

		$routename = explode('.', Route::currentRouteName());
		if (count($routename) > 4) {

			return $routename[3];

		}else if (count($routename) > 3) {

			return $routename[2];

		}else if (count($routename) > 2) {

			return $routename[1];

		}else{

			return $routename[0];

		}

	}


	
	public function module()
	{

		$routename = explode('.', Route::currentRouteName());
		if (count($routename) > 5) {
			return __('label.routename.' . $routename[4]);
		}else if (count($routename) > 4) {
			return __('label.routename.' . $routename[3]);
		}else if (count($routename) > 3) {
			return __('label.routename.' . $routename[2]);
		}else if (count($routename) > 2) {
			return __('label.routename.' . $routename[1]);
		}else{
			return __('label.routename.' . $routename[0]);
		}

	}

	
	public function breadCrumb()
	{

		$routename = explode('.', Route::currentRouteName());

		$i = 0;
		$li = '';
		$active = '';
		foreach ($routename as $key => $value) {
			// GET First
		  if ( ++$i === count($routename) ) {

		  	// Last Active
				if ($value == 'index') {
					$active .= __('label.breadcrumb.crud.index');
				}else if ($value == 'create') {
					$active .= __('label.breadcrumb.crud.create');
				}else if ($value == 'edit') {
					$active .= __('label.breadcrumb.crud.edit');
				}else if ($value == 'image') {
					$active .= __('label.breadcrumb.crud.image');
				}else{
					$active .= __('label.breadcrumb.routename.'. $value);
				}
				$li .= '<li class="breadcrumb-item active">'. $active .'</li>';

		  } else if( $key === 0 ) {
				$li .= '<li class="breadcrumb-item"><a href="'. route($value.'.index') .'">'. __('label.breadcrumb.routename.'. $value) .'</a></li>';
		  }else if ( count($routename) > 3) {
		  	// if length 3 Level deep
				$li .= '<li class="breadcrumb-item"><a href="'. route($routename[1].'.'.$routename[2].'.index') .'">'. __('label.breadcrumb.routename.'. $value) .'</a></li>';
		  }else if ( count($routename) > 4) {
		  	// if length 4 Level deep
				$li .= '<li class="breadcrumb-item"><a href="'. route($routename[1].'.'.$routename[2].'.'.$routename[3].'.index') .'">'. __('label.breadcrumb.routename.'. $value) .'</a></li>';
		  }else{
				if ($value!='basic_data') {
					// if length normal crud
					$li .= '<li class="breadcrumb-item"><a href="'. route($value.'.index') .'">'. __('label.breadcrumb.routename.'. $value) .'</a></li>';
				}
		  }
		  // End if
		}
		// End Foreach
		return $li;

	}

	
	public function subModule()
	{

		$routename = explode('.', Route::currentRouteName());

		$name = '';
		if ( count($routename) > 4 ) {

			$name = $routename[4];

		}else if ( count($routename) > 3 ) {

			$name = $routename[3];

		}else if ( count($routename) > 2 ) {

			$name = $routename[2];

		}else{

			$name = $routename[1];

		}
		
		if ($name == 'index') {
			$name = __('label.content.header.index');
		}else if ($name == 'create') {
			$name = __('label.content.header.create');
		}else if ($name == 'edit') {
			$name = __('label.content.header.edit');
		}else if ($name == 'image') {
			$name = __('label.content.header.image');
		}else if ($name == 'show') {
			$name = __('label.content.header.show');
		}else if ($name == 'pick_year') {
			$name = __('label.content.header.pick_year');
		}else if ($name == 'year') {
			$name = __('label.content.header.year');
		}else if ($name == 'month') {
			$name = __('label.content.header.month');

		}else if ($name == 'role') {
			$name = __('label.content.header.role');

		}else if ($name == 'password') {
			$name = __('label.content.header.password');

		}else if ($name == 'assign_permission') {
			$name = __('label.content.header.assign_permission');

		}else if ($name == 'edit_order') {
			$name = __('label.content.header.edit_order');

		}else if ($name == 'home') {
			$name = '';
		}else{

		}

		return $name;
	}
	
	public function allPermissions()
	{
		$permissions = Permission::orderBy('created_at', 'asc')->get();
		return $permissions;
	}

	
	public function decimalToWord($n)
	{
		$f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
		$nums = explode ('.', $n);
		$whole = $f->format($nums[0]);
		$str_dollar = (($nums[0]> 1 )? " dollars" : " dollar");
		if (count($nums) == 2) {
			$str_cent = (($nums[1]> 1)? " cents" : " cent");
			
			$fraction = $f->format($nums[1]);
			return $whole . $str_dollar . (($nums[1] > 0)? ' and '. $fraction . $str_cent : "");
		} else {
			return $whole . $str_dollar;
		}
	}

}