<div class="modal-body">
  <div class="form-group">
    <div class="row">
      <div class="col-md-12">
        <label for="first_name">Name<span class="red-color">*</span></label>
        <input type="text" class="form-control" name="name" id="name" value="{{ isset($course->name)?$course->name:''; }}">
      </div>
      @if(isset($course) && !empty($course))
        <input type="hidden" name="id" value="{{ isset($course->id)?$course->id:''; }}">
      @endif
    </div>
  </div>
</div>