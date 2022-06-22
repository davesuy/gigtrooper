<div class="input-group box">
    <h3 class="box-title">{!! $title !!}</h3>
</div>
<div class="input-group box">
    <div class="radio-btn-yes-no btn-group">
        <a class="btn btn-primary btn-sm active" data-toggle="answer" data-title="Y">YES</a>
        <a class="btn btn-primary btn-sm notActive" data-toggle="answer" data-title="N">NO</a>
    </div>
    <input type="hidden" name="{{ $type }}[answer]" id="answer" value="Y">
</div>
<div class="input-group">
    <h3 class="box-title">Additional Details:</h3>
    <textarea style="border: 1px solid #838383" cols="40" rows="8" name="{{ $type }}[details]"></textarea>
</div>
<style>
    .radio-btn-yes-no .notActive{
        color: #3276b1;
        background-color: #fff;
    }
</style>
@section('beforebody')
<script>
    jQuery('.radio-btn-yes-no a').on('click', function(){
        var sel = jQuery(this).data('title');
        var tog = jQuery(this).data('toggle');
        jQuery('#'+tog).prop('value', sel);

        jQuery('a[data-toggle="'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
        jQuery('a[data-toggle="'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
    })
</script>
@parent

@endsection