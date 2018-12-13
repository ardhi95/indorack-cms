<div class="login-container">
    <div class="login-box animated fadeInDown">
        <div class="login-logo"></div>
        <div class="login-body">
            <div class="login-title"><strong>Welcome</strong>, Please login</div>
            <?php echo $this->Form->create("User",array(
				"url"	=>	array(
					"controller"	=>	"Account",
					"action"		=>	"Login",
					//"?"				=>	"debug=1"
				),
				"class"	=>	"form-horizontal",
				"novalidate"
			))?>
            
            <?php echo $this->Form->input("email",
				array(
					"div"			=>	array("class"=>"form-group"),
					"label"			=>	false,
					"between"		=>	'<div class="col-md-12">',
					"after"			=>	"</div>",
					"autocomplete"	=>	"off",
					"type"			=>	"text",
					'error' 		=>	array(
						'attributes' => array(
							'wrap' 	=> 'span', 
							'class' => 'error-white'
						)
					),
					"class"			=>	'form-control',
					"format"		=>	array(
						'before', 
						'label', 
						'between', 
						'input', 
						'error',
						'after',
					),
					"placeholder"	=>	"Email"
				)
			)?>
            
            <?php echo $this->Form->input("password",
				array(
					"div"			=>	array("class"=>"form-group"),
					"label"			=>	false,
					"between"		=>	'<div class="col-md-12">',
					"after"			=>	"</div>",
					"autocomplete"	=>	"off",
					"type"			=>	"password",
					'error' 		=>	array(
						'attributes' => array(
							'wrap' 	=> 'span', 
							'class' => 'error-white'
						)
					),
					"class"			=>	'form-control',
					"format"		=>	array(
						'before', 
						'label', 
						'between', 
						'input', 
						'error',
						'after',
					),
					"placeholder"	=>	"Password"
				)
			)?>
            <div class="form-group">
                <div class="col-md-6">
                	<?php
                    	echo $this->Form->input("keep_login",array(
							"type"		=>	"checkbox",
							"div"		=>	false,
							"label"		=>	false,
							"before"	=>	'<label class="check" style="color:white">',
							"after"		=>	' Keep me login</label>',
							"class"		=>	'icheckbox',
							"value"		=>	"1"
						));
					?>
                	<!--label class="check" style="color:white">
                    	<input type="checkbox" class="icheckbox"/> Keep me login
                    </label-->
                </div>
                <div class="col-md-6">
                    <input type="submit" class="btn btn-info btn-block" value="Log In"/>
                </div>
            </div>
            <?php echo $this->Form->end()?>
        </div>
        <div class="login-footer">
            <div class="pull-left">
                &copy; <?php echo date("Y")?> <?php echo $settings['cms_app_name']?>
            </div>
        </div>
    </div>
</div>