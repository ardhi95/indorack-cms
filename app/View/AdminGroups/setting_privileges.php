<?php $this->start("script");?>
<script>
$(document).ready(function(){

	$("#parent_col_1").on('ifChecked', function(event){
		$("input[col^=col_1]").iCheck('check');
	});

	$("#parent_col_1").on('ifUnchecked', function(event){
		$("input[col^=col_1]").iCheck('uncheck');
	});


	$("#parent_col_2").on('ifChecked', function(event){
		$("input[col^=col_2]").iCheck('check');
	});

	$("#parent_col_2").on('ifUnchecked', function(event){
		$("input[col^=col_2]").iCheck('uncheck');
	});

	$("#parent_col_3").on('ifChecked', function(event){
		$("input[col^=col_3]").iCheck('check');
	});

	$("#parent_col_3").on('ifUnchecked', function(event){
		$("input[col^=col_3]").iCheck('uncheck');
	});

	$("#parent_col_4").on('ifChecked', function(event){
		$("input[col^=col_4]").iCheck('check');
	});

	$("#parent_col_4").on('ifUnchecked', function(event){
		$("input[col^=col_4]").iCheck('uncheck');
	});

	$("#parent_col_5").on('ifChecked', function(event){
		$("input[type=checkbox]").iCheck('check');
	});

	$("#parent_col_5").on('ifUnchecked', function(event){
		$("input[type=checkbox]").iCheck('uncheck');
	});

	$("input[col=col_5]").on('ifChecked', function(){
		var row	=	$(this).attr("row").split("_");
		$("input[row=row_"+row[1]+"]").iCheck('check');
	});

	$("input[col=col_5]").on('ifUnchecked', function(){
		var row	=	$(this).attr("row").split("_");
		$("input[row=row_"+row[1]+"]").iCheck('uncheck');
	});
});
</script>
<?php $this->end();?>

<!-- START BREADCRUMB -->
<ul class="breadcrumb push-down-0">
    <li><a href="javascript:void(0);"><?php echo Inflector::humanize(Inflector::underscore($ControllerName))?></a></li>
    <li class="active">
        <?php echo __('Setting Privileges')?>
    </li>
</ul>
<!-- END BREADCRUMB -->

<!-- PAGE TITLE -->
<div class="content-frame">
    <div class="content-frame-top">
        <div class="page-title">
            <h2><span class="fa fa-th-large"></span>
                <?php echo Inflector::humanize(Inflector::underscore($ControllerName))?>
            </h2>
        </div>
    </div>
</div>
<!-- END PAGE TITLE -->

<!-- START PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <div class="row">
        <div class="col-md-12">
        	<?php
				echo $this->Session->flash();
			?>
        	<!-- START BASIC TABLE SAMPLE -->
            <?php echo $this->Form->create("AroAcos", array('url' => array("controller"=>$ControllerName,"action"=>"SettingPrivileges",$ID,$page,$viewpage,"?"=>"debug=1"))); ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo __('Setting Privileges')?></h3>
                </div>
                <div class="panel-body panel-body-table">
                    <div class="table-responsive">
                        <table class="table table-bordered table-actions table-striped">
                            <thead>
                                <tr>
                                    <th>&nbsp;

                                    </th>
                                    <th class="text-center">
                                    	<label>
                                        	<input type="checkbox" id="parent_col_1" class="icheckbox"/><br/>
                                            View
                                        </label>
                                    </th>
                                    <th class="text-center">
                                    	<label>
                                        	<input type="checkbox" id="parent_col_2" class="icheckbox"/><br/>
                                            Add
                                        </label>
                                    </th>
                                    <th class="text-center">
                                    	<label>
                                        	<input type="checkbox" id="parent_col_3" class="icheckbox"/><br/>
                                            Edit
                                        </label>
                                    </th>
                                    <th class="text-center">
                                    	<label>
                                        	<input type="checkbox" id="parent_col_4" class="icheckbox"/><br/>
                                            Delete
                                        </label>
                                    </th>
                                    <th class="text-center">
                                    	<label>
                                        	<input type="checkbox" id="parent_col_5" class="icheckbox"/><br/>
                                            All
                                        </label>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php $count=0;?>
								<?php foreach($data as $data):?>
                                <?php $count++;?>
                                <?php $style = ($data["MyAco"]["parent_id"] == $top["MyAco"]["id"]) ? "style='background-color:#f8fafc'" : "";?>
                                <?php $bold = ($data["MyAco"]["parent_id"] == $top["MyAco"]["id"]) ? "style='font-weight:bold;'" : "";?>
                                <tr>
                                    <td <?php echo $bold?>><?php echo $data["tree_prefix"].$data["MyAco"]["alias"]?></td>
                                    <td class="text-center">
                                        <?php echo $this->Form->input("AroAco.".$data["MyAco"]["id"]."._read",array(
                                            "name"		=>	"data[AroAco][".$data["MyAco"]["id"]."][_read]",
                                            "type"		=>	"checkbox",
                                            "col"		=>	"col_1",
                                            "row"		=>	"row_".$count,
                                            "id"		=>	"chk_".$count."_1",
                                            "label"		=>	false,
                                            "div"		=>	false,
                                            "required"	=>	"",
											"class"		=>	"icheckbox"
                                        ));?>

                                    </td>
                                    <td class="text-center">
                                        <?php echo $this->Form->input("AroAco.".$data["MyAco"]["id"]."._create",array(
                                            "name"		=>	"data[AroAco][".$data["MyAco"]["id"]."][_create]",
                                            "type"		=>	"checkbox",
                                            "col"		=>	"col_2",
                                            "row"		=>	"row_".$count,
                                            "id"		=>	"chk_".$count."_2",
                                            "label"		=>	false,
                                            "div"		=>	false,
                                            "required"	=>	"",
											"class"		=>	"icheckbox"
                                        ));?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $this->Form->input("AroAco.".$data["MyAco"]["id"]."._update",array(
                                            "name"		=>	"data[AroAco][".$data["MyAco"]["id"]."][_update]",
                                            "type"		=>	"checkbox",
                                            "col"		=>	"col_3",
                                            "row"		=>	"row_".$count,
                                            "id"		=>	"chk_".$count."_3",
                                            "label"		=>	false,
                                            "div"		=>	false,
                                            "required"	=>	"",
											"class"		=>	"icheckbox"
                                        ));?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $this->Form->input("AroAco.".$data["MyAco"]["id"]."._delete",array(
                                            "name"		=>	"data[AroAco][".$data["MyAco"]["id"]."][_delete]",
                                            "type"		=>	"checkbox",
                                            "col"		=>	"col_4",
                                            "row"		=>	"row_".$count,
                                            "id"		=>	"chk_".$count."_4",
                                            "label"		=>	false,
                                            "div"		=>	false,
                                            "required"	=>	"",
											"class"		=>	"icheckbox"
                                        ));?>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" id="<?php echo "chk_".$count."_5"?>" col="col_5" row="row_<?php echo $count?>" class="icheckbox"/>
                                    </td>
                            	</tr>
                            	<?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer">
                    <input type="button" class="btn btn-primary pull-right" onclick="location.href = '<?php echo $settings["cms_url"].$ControllerName?>/Index/<?php echo $page?>/<?php echo $viewpage?>'" value="<?php echo __('Cancel')?>"/>
                    <input type="submit" class="btn btn-danger pull-right" value="<?php echo __('Save')?>" style="margin-right:10px;"/>
                </div>
            </div>
            <?php echo $this->Form->end();?>
            <!-- END BASIC TABLE SAMPLE -->
        </div>
    </div>
</div>
<!-- END PAGE CONTENT WRAPPER -->
