<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\VesselController::class, 'update'], [$vessel->id]), 'method' => 'PUT', 'id' => 'vessel_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'vessel.edit_vessel' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'vessel.vessel_name' ) . ':*') !!}
          {!! Form::text('name', $vessel->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'vessel.vessel_name' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('expected_date', __( 'vessel.expected_date' ) . ':*') !!}
          {!! Form::date('expected_date', $vessel->expected_date, ['class' => 'form-control', 'required', 'placeholder' => __( 'vessel.expected_date' ) ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('arrival_date', __( 'vessel.arrival_date' ) . ':*') !!}
          {!! Form::date('arrival_date', $vessel->arrival_date, ['class' => 'form-control', 'required', 'placeholder' => __( 'vessel.arrival_date' ) ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('shipping_line_agent', __( 'vessel.shipping_line_agent' ) . ':*') !!}
          {!! Form::text('shipping_line_agent', $vessel->shipping_line_agent, ['class' => 'form-control', 'required', 'placeholder' => __( 'vessel.shipping_line_agent' ) ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('selling_date', __( 'vessel.selling_date' ) . ':*') !!}
          {!! Form::date('selling_date', $vessel->selling_date, ['class' => 'form-control', 'required', 'placeholder' => __( 'vessel.selling_date' ) ]); !!}
      </div>

      {{-- <div class="form-group">
        {!! Form::label('description', __( 'vessel.short_description' ) . ':') !!}
          {!! Form::text('description', $vessel->description, ['class' => 'form-control','placeholder' => __( 'vessel.short_description' )]); !!}
      </div> --}}

        @if($is_repair_installed)
          <div class="form-group">
             <label>
                {!!Form::checkbox('use_for_repair', 1, $vessel->use_for_repair, ['class' => 'input-icheck']) !!}
                {{ __( 'repair::lang.use_for_repair' )}}
            </label>
            @show_tooltip(__('repair::lang.use_for_repair_help_text'))
          </div>
        @endif

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->