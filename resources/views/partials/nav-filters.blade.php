<div class="col-sm-4 col-md-3">
    <h4 class="search-results-title"><i class="soap-icon-search"></i><b>{{ $total }}</b> results
        found.</h4>
    <div class="toggle-container filters-container">
        <div class="panel style1 arrow-right">
            <h4 class="panel-title">
                <a data-toggle="collapse"
                   href="#country-filter"
                   class="collapsed"
                   aria-expanded="false">Country: {{ $currentCountry }}</a>
            </h4>
            <div id="country-filter" class="panel-collapse collapse" aria-expanded="false"
                 style="height: 0px;">
                <div class="panel-content">
                    {!! Form::open(array('action' => 'SearchMembersController@changeCountry', 'class' => 'form')) !!}
                    <input type="hidden" name="currentUrl" value="{{ \Request::getPathInfo() }}"/>
                    <div class="form-group">
                        {!! \App::make('countryService')->getCountriesDropDown($countryCode) !!}
                    </div>
                    {!! Form::button('Change Country', array(
                    'type' => 'submit',
                    'class' => 'btn-medium uppercase full-width' )) !!}
                    {!! Form::close() !!}
                    <div class="clearer"></div>
                </div><!-- end content -->
            </div>
        </div>

        @if (!empty($subsHtmlDisplayRegion) AND !empty($stateValues))
            <div class="panel style1 arrow-right">
                <h4 class="panel-title">
                    <a data-toggle="collapse"
                       href="#region-filter"
                       class="collapsed"
                       aria-expanded="false">{{ $stateValues['title'] }}: {{ $currentProvince }}</a>
                </h4>
                <div id="region-filter" class="panel-collapse collapse" aria-expanded="false"
                     style="height: 0px;">
                    <div class="panel-content">
                        {!! Form::open(array(
                        'action' => 'SearchMembersController@filterRegion',
                        'class' => 'form')) !!}

                        <input type="hidden" name="currentUrl"
                               value="{{ \Request::getRequestUri() }}"/>
                        <input type="hidden" name="baseUrl"
                               value="/search/members/{{ \Request::segment(3) }}/{{ \Request::segment(4) }}"/>
                        <div class="form-group">
                            {!! $subsHtmlDisplayRegion !!}
                        </div>
                        {!! Form::button('Submit', array(
                        'type' => 'submit',
                        'class' => 'btn-medium uppercase full-width' )) !!}
                        {!! Form::close() !!}
                        <div class="clearer"></div>
                    </div><!-- end content -->
                </div>
            </div>
        @endif

        <div class="panel style1 arrow-right">
            <h4 class="panel-title">
                <a data-toggle="collapse" href="#memberCategory-filter"
                   aria-expanded="true">Category: {{ $currentMemberCategory }}</a>
            </h4>
            <div id="memberCategory-filter" class="panel-collapse collapse in">
                <div class="panel-content">
                    {!! \TemplateHelper::getCategoryNestedMenu($memberCategories, $filterHandles) !!}
                    <div class="clearer"></div>
                </div><!-- end content -->
            </div>
        </div>

        @if (!empty($subsHtmlDisplay))
            <div class="panel style1 arrow-right">
                <h4 class="panel-title">
                    <a data-toggle="collapse"
                       href="#subfield-filter"
                       aria-expanded="true">Filter</a>
                </h4>
                <div id="subfield-filter" class="panel-collapse collapse in">
                    <div class="panel-content">
                        {!! Form::open(array(
                        'action' => 'SearchMembersController@filterElements',
                        'class' => 'form')) !!}
                        <div class="form-group">
                            <input type="hidden" name="currentUrl"
                                   value="{{ \Request::getRequestUri() }}"/>
                            {!! $subsHtmlDisplay !!}
                        </div>
                        {!! Form::button('Submit', array(
                        'type' => 'submit',
                        'class' => 'btn-medium uppercase full-width' )) !!}
                        {!! Form::close() !!}
                        <div class="clearer"></div>
                    </div><!-- end content -->
                </div>
            </div>
        @endif

        <div class="panel style1 arrow-right">
            <h4 class="panel-title">
                <a data-toggle="collapse" href="#fee-filter" class="collapsed"
                   aria-expanded="false">
                    Fee
                    <small> {!! $currentFee !!}</small>
                </a>
            </h4>

            <div id="fee-filter" class="panel-collapse collapse" aria-expanded="false"
                 style="height: 0px;">
                <div class="panel-content">
                    {!! Form::open(array(
                    'action' => 'SearchMembersController@filterElements',
                    'class' => 'form')) !!}
                    <input type="hidden" name="currentUrl" value="{{ \Request::getRequestUri() }}"/>
                    <div class="form-group">
                        {!! $filters['fee'] !!}
                    </div>
                    {!! Form::button('Submit', array(
                        'type' => 'submit',
                        'class' => 'btn-medium uppercase full-width' )) !!}
                    {!! Form::close() !!}
                    <div class="clearer"></div>
                </div><!-- end content -->
            </div>
        </div>
    </div>
</div>

@section('beforebody')
    @parent
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.category-menu .toggle').click(function(e) {
                e.preventDefault();

                var $this = $(this);

                if ($this.next().hasClass('shown')) {
                    $this.next().removeClass('shown');
                    $this.next().slideUp(350);

                    $this.find('i').removeClass('glyphicon-minus');
                    $this.find('i').addClass('glyphicon-plus');


                } else {
                    $this.parent().parent().find('li .inner').removeClass('shown');
                    $this.parent().parent().find('li .inner').slideUp(350);
                    $this.next().toggleClass('shown');
                    $this.next().slideToggle(350);

                    $this.find('i').removeClass('glyphicon-plus');
                    $this.find('i').addClass('glyphicon-minus');

                    $this.parent().siblings().find('i').removeClass('glyphicon-minus');
                    $this.parent().siblings().find('i').addClass('glyphicon-plus');
                }
            });
        });
    </script>
    <script type="text/javascript" src="/js/jquery.ui.touch-punch.min.js"></script>
@endsection