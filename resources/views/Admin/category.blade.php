@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
	<!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">categories List</h3>
                <button type="button" data-toggle="modal" id="new_user" class="right" style="float: right;">Create New Category</button>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                  <div class="row">
                    <div class="col-sm-12">
                      <table id="category-data" class="table table-bordered table-striped dataTable dtr-inline" aria-describedby="example1_info">
                        <thead>
                          <tr>
                            <th>No.</th>
                            <th>Category Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>                  
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
      </div>
    </section>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="create_new_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header border-bottom-0">
            <h5 class="modal-title" id="exampleModalLabel">Category</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form class="user" id="usercreate" method="POST" style=" padding-top: 0px; margin-top: 0px;">
            @csrf
            <div class="user-main-form-body">
              
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-center">
              <button type="submit" class="btn btn-success">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>

@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script type="text/javascript">
	var table = jQuery('#category-data').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
          url: "{{ route('category') }}",
      },
      columns: [
          {data: 'DT_RowIndex', name: 'DT_RowIndex'},
          {data: 'name', name: 'name'},
          {data: 'status', name: 'status'},
          {data: 'view_details', name: 'view_details', orderable: false, searchable: false},
      ]
    });

    jQuery("#usercreate").validate({
      rules: {
          name: {
              required: true,
          },
      },
      submitHandler: function(form) {
          var formdata = new FormData(jQuery('#usercreate').get(0));
          jQuery.ajax({
              type: "POST",
              url: "{{ route('admin-new-category') }}",
              data: formdata,
              processData: false,
              contentType: false,
              cache: false,
              success: function(data) {
                  table.draw();
                  jQuery('#create_new_user').modal('hide');
              },
              error: function(data) {
                  alert("Some Error");
              }
          });
      }
  	});

  	jQuery(document).on('click','.edit,#new_user',function(e){
      e.preventDefault();
       var id = jQuery(this).attr('data-id');
       jQuery.ajax({
        type: "GET",
        url: "{{ route('admin-categoy-edit',"+id+") }}",
        data: {"id":id},
        success: function(data) {
         jQuery('.user-main-form-body').html(data);
         jQuery('#create_new_user').modal();
         //table.draw();
        },
        error: function(data) {
          alert("some Error");
        }
      });
    });

    jQuery(document).on('click','.delete',function(e){
      let text = "Are you sure you want to delete this record!";
      if (confirm(text) != true) {
        return false;
      }
       e.preventDefault();
       var id = jQuery(this).attr('data-id');
       jQuery.ajax({
        type: "GET",
        url: "{{ route('admin-categoy-delete',"+id+") }}",
        data: {"id":id},
        success: function(data) {
          table.draw();
        },
        error: function(data) {
          alert("some Error");
        }
      });
    });

</script>
@endpush