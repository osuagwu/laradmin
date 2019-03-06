@if(!isset($isHeadCheckbox) or $isHeadCheckbox)
<label class="table-row-checkbox-all">
<input title="Select all rows" type="checkbox" class="{{$tableName}}-row-select-checkbox select-all" onclick="jQuery('.{{$tableName}}-row-select-checkbox').prop('checked', $(this).prop('checked'));">
</label>

@else
<label class="table-row-checkbox">
    <input title="Select this row" type="checkbox" class="{{$tableName}}-row-select-checkbox"  value="{{$value}}" onclick="jQuery('.select-all.{{$tableName}}-row-select-checkbox').prop('checked', true); jQuery('.{{$tableName}}-row-select-checkbox').each(function(){if(!$(this).prop('checked')){   $('.select-all.{{$tableName}}-row-select-checkbox').prop('checked', false);    }})">
</label>
@endif