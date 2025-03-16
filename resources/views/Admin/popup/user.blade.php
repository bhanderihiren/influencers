<div class="modal-body">
  <div class="form-group">
    <div class="row">
      <div class="col-md-12">
        <label for="first_name">Name<span class="red-color">*</span></label>
        <input type="text" class="form-control" name="name" id="name" value="{{ isset($category->name)?$category->name:''; }}">
      </div>
      @if(isset($category) && !empty($category))
        <input type="hidden" name="id" value="{{ isset($category->id)?$category->id:''; }}">
      @endif
    </div>
  </div>
</div>