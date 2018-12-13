<?php $active	=	(!empty($this->request->data)) ? "active" : ""?>
<div class="lockscreen-container">
    <div class="lockscreen-box animated fadeInDown <?php echo $active?>">

        <div class="lsb-access">
            <div class="lsb-box">
                <div class="fa fa-lock"></div>
                <div class="user animated fadeIn">
                    <img src="<?php echo $profile['Thumbnail']['host'].$profile['Thumbnail']['url']."?t=".time()?>" alt="John Doe"/>
                    <div class="user_signin animated fadeIn">
                        <div class="fa fa-sign-in"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lsb-form animated fadeInDown">
			  <?php echo $this->Form->create("User",array(
                  "url"	=>	array(
                      "controller"	=>	"Account",
                      "action"		=>	"LockScreen",
                      //"?"				=>	"debug=1"
                  ),
                  "class"	=>	"form-horizontal",
                  "novalidate"
              ))?>
                <div class="form-group">
                    <div class="col-md-12">
						<?php echo $this->Form->input("password",
                            array(
                                "div"			=>	array("class"=>"input-group"),
                                "label"			=>	false,
                                "before"		=>	'<div class="input-group-addon">
                                                        <span class="fa fa-lock"></span>
                                                    </div>',
                                "autocomplete"	=>	"off",
                                "type"			=>	"password",
                                'error' 		=>	false,
                                "class"			=>	'form-control',
                                "placeholder"	=>	"Password"
                            )
                        )?>
                        <?php echo $this->Form->error("User.password",null,array('wrap' 	=> 'span','class' => 'error-white'));?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                    	<button class="btn btn-info btn-block"/>
                            <i class="fa fa-unlock-alt"></i>&nbsp;
                            Unlock
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <a href="<?php echo $settings['cms_url']?>Account/LogOut" class="btn btn-danger btn-block"/>
                        	<i class="fa fa-arrow-left"></i>&nbsp;
                            Sign Out
                        </a>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
