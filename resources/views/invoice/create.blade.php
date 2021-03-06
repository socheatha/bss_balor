@extends('layouts.app')

@section('css')
	<style type="text/css">
	</style>
@endsection

@section('content')
<div class="card">
	<div class="card-header">
		<b>{!! Auth::user()->subModule() !!}</b>
		<div class="card-tools">
			<a href="{{route('invoice.index')}}" class="btn btn-danger btn-sm btn-flat"><i class="fa fa-table"></i> &nbsp;{{ __('label.buttons.back_to_list', [ 'name' => Auth::user()->module() ]) }}</a>
		</div>

		<!-- Error Message -->
		@component('components.crud_alert')
		@endcomponent

	</div>

	{!! Form::open(['url' => route('invoice.store'),'id' => 'submitForm','method' => 'post','class' => 'mt-3', 'autocomplete' => 'off']) !!}
	<div class="card-body">
		@include('invoice.form')

		<div class="card card-outline card-primary mt-4">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-list"></i>&nbsp;
					{{ __('alert.modal.title.invoice_detail') }}
				</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-flat btn-sm btn-success btn-prevent-submit" id="btn_add_item"><i class="fa fa-plus"></i> {!! __('label.buttons.add_item') !!}</button>
				</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body item_list"></div>
			<!-- /.card-body -->
		</div>

	</div>
	<!-- ./card-body -->
	
	<div class="card-footer text-muted text-center">
		@include('components.submit')
	</div>
	<!-- ./card-Footer -->
	{{ Form::close() }}

</div>

@include('invoice.modal')

@endsection

@section('js')
<script type="text/javascript">
	var endLoadScript = function () {} // declear global variable as function
	$(document).ready(function(){
		$('#btn_add_item').click();
	});

	$('[name="pt_province_id"]').change( function(e){
		if ($(this).val() != '') {
			$.ajax({
				url: "{{ route('province.getSelectDistrict') }}",
				method: 'post',
				data: {
					id: $(this).val(),
				},
				success: function (data) {
					$('[name="pt_district_id"]').attr({"disabled":false});
					$('[name="pt_district_id"]').html(data);
					endLoadScript(); endLoadScript = function () {}; // execute this function then remove it
				}
			});
		}else{
			$('[name="pt_district_id"]').attr({"disabled":true});
			$('[name="pt_district_id"]').html('<option value="">{{ __("label.form.choose") }}</option>');
			
		}
	});


	$('.btn-prevent-submit').click(function (event) {
		event.preventDefault();
	});

	$('#btn_add_item').click(function (event) {
		event.preventDefault();
		var id = Math.floor(Math.random() * 1000);
		$('.item_list').append(`<div class="prescription_item" id="${ id }">
		<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						{!! Html::decode(Form::label('service_id', __('label.form.invoice.service')." <small>*</small>")) !!}
						{!! Form::text('service_name[]', '', ['class' => 'form-control service_add', 'data-id'=>'${ id }', 'id'=>'input-service_id-${ id }','placeholder' => 'name','required', 'list' => 'service_list', 'onchange' => 'load_service_info(\'${id}\', this)']) !!}
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						{!! Html::decode(Form::label('price', __('label.form.invoice.price')."($) <small>*</small>")) !!}
						{!! Form::text('price[]', '', ['class' => 'form-control', 'id'=>'input-price-${ id }','placeholder' => 'price','required']) !!}
					</div>
				</div>
				<div class="col-sm-5">
					<div class="form-group">
						{!! Html::decode(Form::label('description', __('label.form.description'))) !!}
						{!! Form::textarea('description[]', '', ['class' => 'form-control', 'id'=>'input-description-${ id }','placeholder' => 'description','style' => 'height: 38px']) !!}
					</div>
				</div>
				<div class="col-sm-1">
					<div class="form-group">
						{!! Html::decode(Form::label('', __('label.buttons.remove'))) !!}
						<div>
							<button class="btn btn-danger btn-flat btn-block btn-prevent-submit" onclick="removeItem(${ id })"><i class="fa fa-trash-alt"></i></button>
						</div>
					</div>
				</div>
			</div>
		</div>`);
	});

	function removeItem(id) {
		$('#'+id).remove();
	}

	$(".select2_pagination").change(function () {
		$('[name="txt_search_field"]').val($('.select2-search__field').val());
	});
	
	function select2_search (term) {
		$(".select2_pagination").select2('open');
		var $search = $(".select2_pagination").data('select2').dropdown.$search || $(".select2_pagination").data('select2').selection.$search;
		$search.val(term);
		$search.trigger('keyup');
	}

	$(".select2_pagination").select2({
		theme: 'bootstrap4',
		placeholder: "{{ __('label.form.choose') }}",
		allowClear: true,
		ajax: {
			url: "{{ route('patient.getSelect2Items') }}",
			method: 'post',
			dataType: 'json',
			data: function(params) {
				return {
						term: params.term || '',
						page: params.page || 1
				}
			},
			cache: true
		}
	});	

	function load_service_info(id, _this){
		_this = $(_this);
		let value = _this.val();
		if($('option[value="'+value+'"]').data('price')) $('#input-price-'+id).val($('option[value="'+value+'"]').data('price'));
		if($('option[value="'+value+'"]').data('description')) $('#input-description-'+id).val($('option[value="'+value+'"]').data('description'));
	}

	$('#patient_id').change(function () {
		if ($(this).val()!='') {
			$.ajax({
				url: "{{ route('patient.getSelectDetail') }}",
				type: 'post',
				data: {
					id : $(this).val()
				},
			})
			.done(function( result ) {
				// $('[name="pt_no"]').val(result.patient.no);
				$('[name="pt_name"]').val(result.patient.name);
				$('[name="pt_phone"]').val(result.patient.phone);
				$('[name="pt_age"]').val(result.patient.age);
				$('[name="pt_gender"]').val(result.patient.pt_gender);
				$('[name="pt_village"]').val(result.patient.address_village);
				$('[name="pt_commune"]').val(result.patient.address_commune);
				
				endLoadScript = function () { $('[name="pt_district_id"]').val(result.patient.address_district_id).trigger('change'); } // set task for function waiting for execute
				$('[name="pt_province_id"]').val(result.patient.address_province_id).trigger('change');
			});
		}
		
	});



</script>
@endsection