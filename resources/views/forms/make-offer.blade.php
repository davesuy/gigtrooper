<div class="input-group box">
    <h3 class="box-title">Make an Offer <small>(Enter your rate based on the quote details)</small> </h3>
{{ $currency }} <input type="number" name="{{ $type }}[rate]"
                      placeholder="Make an Offer" step="100" min="0" value="500" />
</div>

<div class="input-group">
    <h3 class="box-title">Additional Details:</h3>
    <textarea style="border: 1px solid #838383" cols="40" rows="8" name="{{ $type }}[details]"></textarea>
</div>