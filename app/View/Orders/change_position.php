<?php $this->start("script");?>
<script>
$(document).ready(function()
{
    var updateOutput = function(e)
    {
        var list   = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.html(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
        } else {
            output.html('JSON browser support required for this demo.');
        }
    };

    // activate Nestable for list 1
    $('#nestable').nestable({
        group: 1
    })
    .on('change', updateOutput);



    // output initial serialised data
    updateOutput($('#nestable').data('output', $('#nestable-output')));
});
</script>
<?php $this->end();?>

<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Home</a></li>
    <li><a href="#">Pages</a></li>
    <li class="active">Nestable</li>
</ul>
<!-- END BREADCRUMB -->

<!-- PAGE TITLE -->
<div class="page-title">                    
    <h2><span class="fa fa-arrow-circle-o-left"></span> Nestable</h2>
</div>
<!-- END PAGE TITLE -->
                
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    
    <div class="row">
        <div class="col-md-12">
            
            <!-- NESTABLE -->                            
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Nestable List</h3>
                    <p>Drag & drop hierarchical list with mouse and touch compatibility (jQuery plugin)</p>
                    
                    <div class="row">
                    	<div  id="nestable-output"></div >
                        <div class="col-md-6">
                            
                            <div class="dd" id="nestable">
                                <ol class="dd-list" id="Check">
                                    <li class="dd-item" data-id="1">
                                        <div class="dd-handle">Item 1</div>
                                    </li>
                                    <li class="dd-item" data-id="2">
                                        <div class="dd-handle">Item 2</div>
                                        <ol class="dd-list">
                                            <li class="dd-item" data-id="3"><div class="dd-handle">Item 3</div></li>
                                            <li class="dd-item" data-id="4"><div class="dd-handle">Item 4</div></li>
                                            <li class="dd-item" data-id="5">
                                                <div class="dd-handle">Item 5</div>
                                                <ol class="dd-list">
                                                    <li class="dd-item" data-id="6"><div class="dd-handle">Item 6</div></li>
                                                    <li class="dd-item" data-id="7"><div class="dd-handle">Item 7</div></li>
                                                    <li class="dd-item" data-id="8"><div class="dd-handle">Item 8</div></li>
                                                </ol>
                                            </li>
                                            <li class="dd-item" data-id="9"><div class="dd-handle">Item 9</div></li>
                                            <li class="dd-item" data-id="10"><div class="dd-handle">Item 10</div></li>
                                        </ol>
                                    </li>
                                    <li class="dd-item" data-id="11">
                                        <div class="dd-handle">Item 11</div>
                                    </li>
                                    <li class="dd-item" data-id="12">
                                        <div class="dd-handle">Item 12</div>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
            </div>                            
            <!-- END NESTABLE -->

        </div>
    </div>
                                            
</div>
<!-- END PAGE CONTENT WRAPPER -->