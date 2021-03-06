@extends('layouts.app')

@section('css')
	{{-- {{ Html::style('/css/echoes-print-style.css') }} --}}
	<style type="text/css">
	
	</style>
@endsection

@section('content')
<div class="card">
	<div class="card-header">
		<b>{!! Auth::user()->subModule() !!}</b>
		<div class="card-tools">
			<a href="{{route('echoes.index', $type)}}" class="btn btn-danger btn-sm btn-flat"><i class="fa fa-table"></i> &nbsp;{{ __('label.buttons.back_to_list', [ 'name' => Auth::user()->module() ]) }}</a>
		</div>

		<!-- Error Message -->
		@component('components.crud_alert')
		@endcomponent

	</div>

	{!! Form::open(['url' => route('echoes.store', $type),'method' => 'post', 'enctype'=>'multipart/form-data', 'class' => 'mt-3']) !!}
	<div class="card-body">
		@include('echoes.form')

	</div>
	<!-- ./card-body -->
	
	<div class="card-footer text-muted text-center">
		@include('components.submit')
	</div>
	<!-- ./card-Footer -->
	{{ Form::close() }}

</div>
	
<br/>
<br/>
<br/>
<br/>

@endsection

@section('js')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
		var endLoadScript = function () {}
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
						endLoadScript(); endLoadScript = function () {}; 
					}
				});
			}else{
				$('[name="pt_district_id"]').attr({"disabled":true});
				$('[name="pt_district_id"]').html('<option value="">{{ __("label.form.choose") }}</option>');
				
			}
		});

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


		var editor = CKEDITOR.replace('my-editor', {
			height: '350',
			font_names: 'Calibrib Bold; Calibri Italic; Calibri; Roboto Regular; Roboto Bold; Khmer OS Battambang; Khmer OS Muol Light; Khmer OS Content; Khmer OS Kuolen;',
			toolbar: [
				{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
				{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
				{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'ExportPdf', 'Preview', 'Print', '-', 'Templates' ] },
				{ name: 'insert', items: ['Table' ] },
				{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
				{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
				{ name: 'clipboard', groups: [ 'clipboard', 'undo' ]},
			]
		});

		

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
					$('[name="pt_no"]').val(result.patient.no);
					$('[name="pt_name"]').val(result.patient.name);
					$('[name="pt_phone"]').val(result.patient.phone);
					$('[name="pt_age"]').val(result.patient.age);
					$('[name="pt_gender"]').val(result.patient.pt_gender);
					$('[name="pt_village"]').val(result.patient.address_village);
					$('[name="pt_commune"]').val(result.patient.address_commune);

					endLoadScript = function () {
						$('[name="pt_district_id"]').val(result.patient.address_district_id).trigger('change');
					}
					$('[name="pt_province_id"]').val(result.patient.address_province_id).trigger('change');
				});
			}
			
		});

		

		$('#echo_default_description_id').change(function () {
			if ($(this).val()!='') {
				$.ajax({
					url: "{{ route('echo_default_description.getDetail') }}",
					type: 'post',
					data: {
						id : $(this).val()
					},
				})
				.done(function( result ) {
					editor.setData(result.echo_default_description.description);
				});
			}
			
		});


</script>
@endsection