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
                <h3 class="card-title">User List</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                  <div class="row">
                    <div class="col-sm-12">
                      <table id="user-data" class="table table-bordered table-striped dataTable dtr-inline" aria-describedby="example1_info">
                        <thead>
                          <tr>
                            <th>No.</th>
                            <th>Full Name</th>
                            <th>Phone Number</th>
                            <th>Role</th>
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
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script type="text/javascript">
    var table = jQuery('#user-data').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
          url: "{{ route('admin-user') }}",
      },
      columns: [
          {data: 'DT_RowIndex', name: 'DT_RowIndex'},
          {data: 'name', name: 'name'},
          {data: 'contact_no', name: 'phone_number'},
          {data: 'role', role: 'role'},
          {data: 'status', name: 'status'},
          {data: 'view_details', name: 'view_details', orderable: false, searchable: false},
      ]
    });
    
    $(document).on('click','.delete-details', function(event) {
        var dataid = jQuery(this).attr('data-id');
        jQuery('#deleteModal #confirmDeleteBtn').attr('data-id',dataid);
        jQuery('#deleteModal').modal();
    });

    $('#confirmDeleteBtn').on('click', function() {
        $('#loadingSpinner').removeClass('d-none'); // Show the loading spinner
        var userIdToDelete = jQuery(this).attr('data-id')
        $.ajax({
            url: '{{url("/admin/user/delete/")}}/' + userIdToDelete, // URL to your delete route
            type: 'GET',
            success: function(response) {
                $('#deleteModal').modal('hide'); // Close the modal
                $('#loadingSpinner').addClass('d-none'); // Hide the loading spinner
                
                if (response.status === 'success') {
                   table.draw();
                } else {
                    table.draw();
                }
            },
            error: function(xhr, status, error) {
                $('#loadingSpinner').addClass('d-none'); // Hide the loading spinner
                alert('An error occurred while deleting the user.');
            }
        });
    });
  
    
</script>
@endpush
