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
                          <th>Reason</th>
                          <th>Report Note</th>
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
@endsection

@push('js')

<script type="text/javascript">
	var table = jQuery('#category-data').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
          url: "{{ route('review.report') }}",
      },
      columns: [
          {data: 'DT_RowIndex', name: 'DT_RowIndex'},
          {data: 'reason', name: 'reason'},
          {data: 'description', name: 'description'},
          {data: 'status', name: 'status'},
      ]
    });

    jQuery(document).on('change','.user_status',function() {
      var status = jQuery(this).val();
      var id     = jQuery(this).attr('data-id');
      if (status) {
        jQuery.ajax({
            type: "GET",
            url: "reported-review/status/" + id, // You need to implement this route
            data: {
              status: status,
            },
            success: function(data) {
              table.draw(); 
            }
        });
      }
    });
</script>
@endpush