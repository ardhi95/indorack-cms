<?php
class ApidevController extends AppController
{
	public $uses			=	NULL;
	public $settings 		=	false;
	public $debug 			=	false;
	public $components 		=	array("General");

	public function beforeFilter()
	{
		/*//prepare for logging
		CakeLog::config('apiLog', array(
			'engine' => 'File'
		));
		$requestLog = "\n===========START===========\n";
		foreach($_REQUEST as $key => $requested) {
			$requestLog .= "request['".$key."'] = ".$requested."\n";
		}
		$requestLog .= "===========END===========\n";
		CakeLog::write('apiLog', $requestLog);*/
		
		$this->autoRender = false;
		$this->autoLayout = false;
		
		define("ERR_00",__("Success"));
		define("ERR_01",__("Wrong username or password"));
		define("ERR_02",__("Data not found"));
		define("ERR_03",__("Validate Failed"));
		define("ERR_04",__("Parameter Not Completed!"));
		define("ERR_05",__("Failed send verification code!"));
		$token			=	(isset($_REQUEST['token'])) ? $_REQUEST['token'] : "";

		if($token !== "461fd77b-1f04-4cf9-a045-49fb07435913")
		{
			echo json_encode(array("status"=>false,"message"=>__("Invalid Token"),"data"=>NULL,"code"=>"01"));
			exit;
		}
		
		//SETTING
		$this->settings = Cache::read('settings', 'long');
		if(!$this->settings || (isset($_GET['debug']))) {

			$this->loadModel('Setting');
			$settings			=	$this->Setting->find('first');
			$this->settings		=	$settings['Setting'];
			Cache::write('settings', $this->settings, 'long');
		}
	}

	function TestSendGcm()
	{
		$res = array();
        $res['data']['title'] 			= "Test";
        $res['data']['is_background'] 	= false;
        $res['data']['message'] 		= "Coba kirim";
        $res['data']['image'] 			= "";
        $res['data']['payload'] 		= array();
        $res['data']['timestamp'] 		= date('Y-m-d G:i:s');
		
		$fields = array(
            'to' 		=> "feZU8uQiqOQ:APA91bHt2f8ZHL6WOjNMD8UZFD_Vhh2cZPhg5KRSZwx1Knrv95aOIIoinr0USzrcUOpCEAuIOpC6kcWTLDXQVnzw6SqFSYbkOrv-ZfoX9unFR2BLuItjeE9yM0xd8tpSeWqFBgDvZBqm",
            'data' 		=> $res
        );
		
		// Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . 'AAAAAvM9NBg:APA91bHQVSXsGFL3pk99yN_VbC8QNYiTnJIIRqaziOGcqazK82v_D3zax_rVQvME2q45lh8FyPf53OkTaVlp1c2lLh34vlbVOnw3wJ5ktPuitn3qHyYDTrq6EiurseQyH3XqnwfpiD4y',
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
		
		echo "<pre>";
		print_r($result);
		echo "</pre>";
	}
	
	
	function UpdateGcmId()
	{
		$gcm_id		=	isset($_REQUEST['gcm_id']) ? (!empty($_REQUEST['gcm_id']) ? $_REQUEST['gcm_id'] : NULL) : NULL;
		
		$user_id	=	isset($_REQUEST['user_id']) ? (!empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : NULL) : NULL;
		
		$this->loadModel("User");
		$check	=	$this->User->find("first",array(
						"conditions"	=>	array(
							"User.id"	=>	$user_id
						)
					));
					
		if(!empty($check))
		{
			$this->User->updateAll(
				array(
					"gcm_id"		=>	NULL
				),
				array(
					"User.gcm_id"	=>	$gcm_id
				)
			);
			
			$this->User->updateAll(
				array(
					"gcm_id"		=>	"'".$gcm_id."'"
				),
				array(
					"User.id"		=>	$user_id
				)
			);
		}
	}
	
	
	function UpdateUserLocation()
	{
		$user_id	=	isset($_REQUEST['user_id']) ? (!empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : NULL) : NULL;
		
		$latitude	=	isset($_REQUEST['latitude']) ? (!empty($_REQUEST['latitude']) ? $_REQUEST['latitude'] : NULL) : NULL;
		
		$longitude	=	isset($_REQUEST['longitude']) ? (!empty($_REQUEST['longitude']) ? $_REQUEST['longitude'] : NULL) : NULL;
		
		$this->loadModel("User");
		$check	=	$this->User->find("first",array(
						"conditions"	=>	array(
							"User.id"	=>	$user_id
						)
					));
					
		if(!empty($check) && $longitude != null && $latitude != null)
		{
			$this->User->updateAll(
				array(
					"current_latitude"		=>	$latitude,
					"current_longitude"		=>	$longitude
				),
				array(
					"User.id"	=>	$user_id
				)
			);
		}
		
		$out			=	array(
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function UpdateNotificationDeliverStatus()
	{
		$notification_group_id		=	isset($_REQUEST['notification_group_id']) ? (!empty($_REQUEST['notification_group_id']) ? $_REQUEST['notification_group_id'] : NULL) : NULL;
		
		$user_id					=	isset($_REQUEST['user_id']) ? (!empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : NULL) : NULL;
		
		
		$this->loadModel("User");
		$this->loadModel("Notification");
		$check	=	$this->User->find("first",array(
						"conditions"	=>	array(
							"User.id"	=>	$user_id
						)
					));
					
		if(!empty($check))
		{
			$this->Notification->updateAll(
				array(
					"is_arrival"		=>	"1",
					"arrival_date"		=>	"'".date("Y-m-d H:i:s")."'"
				),
				array(
					"Notification.user_id"	=>	$user_id,
					"Notification.notification_group_id"	=>	$notification_group_id
				)
			);
		}
		
		$out			=	array(
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function UpdateNotificationReadedStatus()
	{
		$notification_group_id		=	isset($_REQUEST['notification_group_id']) ? (!empty($_REQUEST['notification_group_id']) ? $_REQUEST['notification_group_id'] : NULL) : NULL;
		
		$user_id					=	isset($_REQUEST['user_id']) ? (!empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : NULL) : NULL;
		
		$id		=	isset($_REQUEST['id']) ? (!empty($_REQUEST['id']) ? $_REQUEST['id'] : NULL) : NULL;
		$totalNotRead	=	0;
		
		$this->loadModel("User");
		$this->loadModel("Notification");
		$check	=	$this->User->find("first",array(
						"conditions"	=>	array(
							"User.id"	=>	$user_id
						)
					));
					
		if(!empty($check))
		{
			$conditions	=	array(
								"Notification.user_id"					=>	$user_id,
								"Notification.notification_group_id"	=>	$notification_group_id
							);
							
			if(!empty($id))
			{
				$conditions	=	array(
									"Notification.user_id"	=>	$user_id,
									"Notification.id"		=>	$id
								);
			}
			
			$this->Notification->updateAll(
				array(
					"is_readed"			=>	"1",
					"arrival_date"		=>	"'".date("Y-m-d H:i:s")."'"
				),
				$conditions
			);
			
			$totalNotRead		=	$this->Notification->find("count",array(
									"conditions"	=>	array(
										"Notification.user_id"		=>	$user_id,
										"Notification.is_readed"	=>	"0"
									)
								));
		}
		
		$out			=	array(
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES,
			"totalNotRead"			=>	$totalNotRead
		);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function GetDataNeeded()
	{
		$status				=	false;
		$message			=	ERR_04;
		$code				=	"04";
		$data				=	array();
		$user_id			=	NULL;
		$User				=	array();
		$Setting			=	array();
		$Notif				=	array();
		$NavigationMenu		=	array();
		$TotalNotif			=	0;
		
		if(isset($_REQUEST['user_id']))
			$user_id	=	$_REQUEST['user_id'];

		if(!empty($user_id))
		{
			$this->loadModel("User");
			$this->User->BindDefault();
			$this->User->VirtualFieldActivated();
			$userDetail	=	$this->User->find("first",array(
								"conditions"	=>	array(
									"User.id"		=>	$user_id,
									"User.status"	=>	"1"
								),
								"fields"		=>	array(
									"User.*",
									"Images.*"
								)
							));
							
			if(!empty($userDetail))
			{
				$User	=	$userDetail;
			}
			
			//CHECK NOTIFICATION
			$this->loadModel("Notification");
			$Notif	=	$this->Notification->find("all",array(
							"conditions"	=>	array(
								"Notification.user_id"		=>	$user_id,
								"Notification.is_arrival"	=>	0
							)
						));
						
			$TotalNotif	=	$this->Notification->find("count",array(
								"conditions"	=>	array(
									"Notification.user_id"		=>	$user_id,
									"Notification.is_readed"	=>	0
								)
							));
						
			$this->Notification->updateAll(
				array(
					"is_arrival"	=>	"1",
					"arrival_date"	=>	"'".date("Y-m-d H:i:s")."'"
				),
				array(
					"Notification.user_id"		=>	$user_id
				)
			);
		}
		
		$this->loadModel("NavigationMenu");
		$NavigationMenu	=	$this->NavigationMenu->find("all",array(
								"order"	=>	"NavigationMenu.id asc"
							));
							
		//VEHICLE
		$this->loadModel("Vehicle");
		$this->Vehicle->BindDefault(false);
		$Vehicle		=	$this->Vehicle->find("all",array(
								"conditions"	=>	array(
									"Vehicle.status"	=>	"1"
								),
								"order"			=>	"Vehicle.vehicle_no ASC"
							));	

		$out			=	array(
			"User"					=>	$User,
			"Notif"					=>	$Notif,
			"TotalNotif"			=>	$TotalNotif,
			"NavigationMenu"		=>	$NavigationMenu,
			"Setting"				=>	$this->settings,
			"Vehicle"				=>	$Vehicle,
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);


		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	function UserLogout()
	{
		$status			=	false;
		$message		=	ERR_04;
		$code			=	"04";
		$data			=	array();
        $checkinDetail  =   array();

		$user_id		=	isset($_REQUEST["user_id"]) ? (!empty($_REQUEST["user_id"]) ? $_REQUEST["user_id"] : NULL) : NULL;
		
		$this->loadModel("User");
		$checkUser		=	$this->User->find("first",array(
								"conditions"	=>	array(
									"User.id"		=>	$user_id,
									"User.status"	=>	"1"
								)
							));
							
		if(!empty($checkUser))
		{
			$this->User->updateAll(
				array(
					"gcm_id"	=>	NULL
				),
				array(
					"User.id"	=>	$user_id
				)
			);
		}
		
	}
	
	function Login()
	{
		$status			=	false;
		$message		=	ERR_04;
		$code			=	"04";
		$data			=	array();
        $checkinDetail  =   array();

		$request["User"]["email"]				=	empty($_REQUEST["email"]) ? "" : $_REQUEST["email"];
		$request["User"]["password"]			=	empty($_REQUEST["password"]) ? "" : $_REQUEST["password"];
		$request["User"]["gcm_id"]				=	isset($_REQUEST["gcm_id"]) ? (!empty($_REQUEST["gcm_id"]) ? $_REQUEST["gcm_id"] : NULL) : NULL;
		

		$this->loadModel('User');
		$this->User->set($request);
		$this->User->ValidateLoginCustomer();


		$error									=	$this->User->InvalidFields();
		if(empty($error))
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";

			$this->User->BindDefault(true);
			$this->User->VirtualFieldActivated();
		
			$data		=	$this->User->find('first',array(
								"conditions"	=>	array(
									"User.email"		=>	$request["User"]["email"],
									"User.password"		=>	$this->General->my_encrypt($request["User"]["password"]),
									"User.status"		=>	"1"
								)
							));
							
			if (!empty($request["User"]["gcm_id"]))
			{
				$this->User->updateAll(
					array(
						"gcm_id"				=>	NULL
					),
					array(
						"User.gcm_id"			=>	$request["User"]["gcm_id"]
					)
				);
				
				$this->User->updateAll(
					array(
						"gcm_id"				=>	"'".$request["User"]["gcm_id"]."'"
					),
					array(
						"User.id"				=>	$data["User"]["id"]
					)
				);
			}
		}
		else
		{
			$status		=	false;
			foreach($error as $k => $v)
			{
				$message	=	$v[0];
				break;
			}
			$code		=	"03";
			$data		=	null;
		}

		$out			=	array(
			"status"				=>	$status,
			"message"				=>	$message,
			"data"					=>	$data,
			"code"					=>	$code,
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);


		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function TaskList()
	{
		$status					=	false;
		$message				=	ERR_03;
		$data					=	null;
		$code					=	"03";
		$user_id				=	$_REQUEST['user_id'];
		$page					=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		$task_type_id			=	(empty($_REQUEST['task_type_id'])) ? 1 : $_REQUEST['task_type_id'];
		
		//CHECK USER ID
		$this->loadModel("User");
		$check			=	$this->User->find("first",array(
								"conditions"	=>	array(
									"User.is_admin"	=>	"1",
									"User.id"		=>	$user_id
								)
							));
		
		if(empty($check))
		{				
			$out	=	array(
							"status"	=>	false,
							"message"	=>	"Not authorized",
							"data"		=>	array(),
							"code"		=>	"00",
							"request"	=>	$_REQUEST
						);
			
			$json		=	json_encode($out);
			$this->response->type('json');
			$this->response->body($json);
			return;
		}
		
		
		$this->loadModel("Task");
		$joins			=	array(
								array(
									"table"			=>	"orders",
									"alias"			=>	"Order",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"Task.order_id	=	Order.id"
									)
								),
								array(
									"table"			=>	"task_statuses",
									"alias"			=>	"TaskStatus",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"Task.status	=	TaskStatus.id"
									)
								),
								array(
									"table"			=>	"users",
									"alias"			=>	"Customer",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"Order.customer_id	=	Customer.id"
									)
								)
						);
		
		
		$conditions			=	array(
									"Task.task_type_id"			=>	$task_type_id
								);
								
		$this->paginate		=	array(
			"Task"	=>	array(
				"order"			=>	"Task.id desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"joins"			=>	$joins,
				"conditions"	=>	$conditions,
				"fields"		=>	array(
					"Task.id",
					"Task.order_id",
					"Order.order_no",
					"Order.delivery_no",
					"Order.address",
					"Order.delivery_date",
					"Order.is_urgent",
					"TaskStatus.id",
					"TaskStatus.color",
					"TaskStatus.name",
					"Order.delivery_type_id",
					"Customer.firstname",
					"Customer.lastname"
				)
			)
		);
		
		try
		{
			$fData			=	$this->paginate("Task");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['Task']['pageCount'],
						"page"		=>	$this->params['paging']['Task']['page'],
						"totalData"	=>	$this->params['paging']['Task']['count'],
						"nextPage"	=>	$this->params['paging']['Task']['nextPage'],
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function PickupList()
	{
		$status					=	false;
		$message				=	ERR_03;
		$data					=	null;
		$code					=	"03";
		$user_id				=	$_REQUEST['user_id'];
		$page					=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		
		//CHECK USER ID
		$this->loadModel("User");
		$check					=	$this->User->find("first",array(
										"conditions"	=>	array(
											"User.is_admin"	=>	"1",
											"User.id"		=>	$user_id
										)
									));
		
		if(empty($check))
		{				
			$out	=	array(
							"status"	=>	false,
							"message"	=>	"Not authorized",
							"data"		=>	array(),
							"code"		=>	"00",
							"request"	=>	$_REQUEST
						);
			
			$json		=	json_encode($out);
			$this->response->type('json');
			$this->response->body($json);
			return;
		}
		
		
		$this->loadModel("Order");
		$this->Order->bindModel(
		array(
			"hasMany"	=>	array(
				"OrderProduct"
			),
			"belongsTo"	=>	array(
				"Customer"	=>	array(
					"className"	=>	"User"
				),
				"PickupStatus"	=>	array(
					"className"		=>	"TaskStatus",
					"foreignKey"	=>	"pickup_status"
				)
			)
		),false);
		$this->Order->OrderProduct->bindModel(array(
			"belongsTo"	=>	array(
				"Product"
			)
		),false);
		
		$this->Order->Customer->virtualFields = array(
			"fullname"		=> "CONCAT(Customer.firstname,' ',Customer.lastname)",
		);
		
		
		$conditions			=	array(
									"Order.delivery_type_id"	=>	"2"
								);
								
		$this->paginate		=	array(
			"Order"	=>	array(
				"order"			=>	"Order.id desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"recursive"		=>	3,
				"conditions"	=>	$conditions
			)
		);
		
		try
		{
			$fData			=	$this->paginate("Order");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['Order']['pageCount'],
						"page"		=>	$this->params['paging']['Order']['page'],
						"totalData"	=>	$this->params['paging']['Order']['count'],
						"nextPage"	=>	$this->params['paging']['Order']['nextPage'],
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function OrderList()
	{
		$status			=	false;
		$message		=	ERR_03;
		$data			=	null;
		$code			=	"03";
		$user_id		=	$_REQUEST['user_id'];
		$page			=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		
		//CHECK USER ID
		$this->loadModel("User");
		$check			=	$this->User->find("first",array(
								"conditions"	=>	array(
									"User.is_admin"	=>	"1",
									"User.id"		=>	$user_id
								)
							));
		
		if(empty($check))
		{				
			$out	=	array(
							"status"	=>	false,
							"message"	=>	"Not authorized",
							"data"		=>	array(),
							"code"		=>	"00",
							"request"	=>	$_REQUEST
						);
			
			$json		=	json_encode($out);
			$this->response->type('json');
			$this->response->body($json);
			return;
		}
		
		
		$this->loadModel("Order");
		$joins			=	array(
								array(
									"table"			=>	"order_statuses",
									"alias"			=>	"OrderStatus",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"Order.status	=	OrderStatus.id"
									)
								),
								array(
									"table"			=>	"users",
									"alias"			=>	"Customer",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"Order.customer_id	=	Customer.id"
									)
								)
						);
		
		
		$conditions			=	array();
		$this->paginate		=	array(
			"Order"	=>	array(
				"order"			=>	"Order.id desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"joins"			=>	$joins,
				"conditions"	=>	$conditions,
				"fields"		=>	array(
					"Order.*",
					"OrderStatus.*",
					"Customer.*"
				)
			)
		);
		
		try
		{
			$fData			=	$this->paginate("Order");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['Order']['pageCount'],
						"page"		=>	$this->params['paging']['Order']['page'],
						"totalData"	=>	$this->params['paging']['Order']['count'],
						"nextPage"	=>	$this->params['paging']['Order']['nextPage'],
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function DriverJobList()
	{
		$status			=	false;
		$message		=	ERR_03;
		$data			=	null;
		$code			=	"03";
		$user_id		=	$_REQUEST['user_id'];
		$page			=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		
		//CHECK USER ID
		$this->loadModel("User");
		$check			=	$this->User->find("first",array(
								"conditions"	=>	array(
									"User.is_admin"	=>	"1",
									"User.aro_id"	=>	"6",
									"User.id"		=>	$user_id
								)
							));
		
		if(empty($check))
		{				
			$out	=	array(
							"status"	=>	false,
							"message"	=>	"Not authorized",
							"data"		=>	array(),
							"code"		=>	"00",
							"request"	=>	$_REQUEST
						);
			
			$json		=	json_encode($out);
			$this->response->type('json');
			$this->response->body($json);
			return;
		}
		
		
		$this->loadModel("JobAssign");
		$joins			=	array(
								array(
									"table"			=>	"orders",
									"alias"			=>	"Order",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"JobAssign.order_id		=	Order.id"
									)
								),
								array(
									"table"			=>	"order_statuses",
									"alias"			=>	"OrderStatus",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"Order.status	=	OrderStatus.id"
									)
								),
								array(
									"table"			=>	"users",
									"alias"			=>	"Customer",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"Order.customer_id	=	Customer.id"
									)
								)
							);
		
		
		$conditions			=	array(
									"JobAssign.driver_id"	=>	$user_id
								);
		$this->paginate		=	array(
			"JobAssign"	=>	array(
				"order"			=>	"JobAssign.id desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"joins"			=>	$joins,
				"conditions"	=>	$conditions,
				"fields"		=>	array(
					"JobAssign.*",
					"Order.*",
					"OrderStatus.*",
					"Customer.*"
				)
			)
		);
		
		try
		{
			$fData			=	$this->paginate("JobAssign");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['JobAssign']['pageCount'],
						"page"		=>	$this->params['paging']['JobAssign']['page'],
						"totalData"	=>	$this->params['paging']['JobAssign']['count'],
						"nextPage"	=>	$this->params['paging']['JobAssign']['nextPage'],
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function TechnicianJobList()
	{
		$status			=	false;
		$message		=	ERR_03;
		$data			=	null;
		$code			=	"03";
		$user_id		=	$_REQUEST['user_id'];
		$page			=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		
		//CHECK USER ID
		$this->loadModel("User");
		$check			=	$this->User->find("first",array(
								"conditions"	=>	array(
									"User.is_admin"	=>	"1",
									"User.aro_id"	=>	array("5","6"),
									"User.id"		=>	$user_id
								)
							));
		
		if(empty($check))
		{				
			$out	=	array(
							"status"	=>	false,
							"message"	=>	"Not authorized",
							"data"		=>	array(),
							"code"		=>	"00",
							"request"	=>	$_REQUEST
						);
			
			$json		=	json_encode($out);
			$this->response->type('json');
			$this->response->body($json);
			return;
		}
		
		
		$this->loadModel("TaskHistory");
		$joins			=	array(
								array(
									"table"			=>	"orders",
									"alias"			=>	"Order",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"TaskHistory.order_id		=	Order.id"
									)
								),
								array(
									"table"			=>	"tasks",
									"alias"			=>	"Task",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"TaskHistory.task_id		=	Task.id"
									)
								),
								array(
									"table"			=>	"order_statuses",
									"alias"			=>	"OrderStatus",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"OrderStatus.id				=	Order.status"
									)
								),
								array(
									"table"			=>	"task_statuses",
									"alias"			=>	"TaskStatus",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"TaskHistory.status	=	TaskStatus.id"
									)
								)
							);
		
		
		$conditions			=	array(
									"TaskHistory.employee_id"	=>	$user_id,
									"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")"
								);
								
		$this->paginate		=	array(
			"TaskHistory"	=>	array(
				"order"			=>	"TaskHistory.id desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"joins"			=>	$joins,
				"conditions"	=>	$conditions,
				"fields"		=>	array(
					"TaskHistory.*",
					"Task.task_type_id",
					"Order.*",
					"OrderStatus.*",
					"TaskStatus.*"
				),
				"group"			=>	"TaskHistory.task_id"
			)
		);
		
		try
		{
			$fData			=	$this->paginate("TaskHistory");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['TaskHistory']['pageCount'],
						"page"		=>	$this->params['paging']['TaskHistory']['page'],
						"totalData"	=>	$this->params['paging']['TaskHistory']['count'],
						"nextPage"	=>	$this->params['paging']['TaskHistory']['nextPage'],
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function detailOrder()
	{
		$status				=	true;
		$message			=	ERR_02;
		$code				=	"02";
		$data				=	array();
		$order_id			=	NULL;
		$driver				=	array();
		$technicianWhoRun	=	array();
		
		if(isset($_REQUEST['order_id']))
			$order_id	=	$_REQUEST['order_id'];
		
		if(isset($_REQUEST['user_id']))
			$user_id	=	$_REQUEST['user_id'];
		
		//CHECK USER ACCESS
		$this->loadModel("User");
		$checkUser	=	$this->User->find("first",array(
							"conditions"		=>	array(
								"User.id"		=>	$user_id,
								"User.status"	=>	"1"
							)
						));
						
		if(empty($checkUser))
		{
			$status			=	false;
			$message		=	"You don't have priveledges";
		}
		else
		{
			$this->loadModel("Order");
			$this->loadModel("TaskHistory");
			
			$this->Order->bindModel(array(
				// "hasMany"	=>	array(
				// 	"OrderProduct"
				// ),
				"belongsTo"	=>	array(
					"DeliveryStatus"	=>	array(
						"className"		=>	"task_statuses",
						"foreignKey"	=>	"delivery_status"
					),
					"PickupStatus"	=>	array(
						"className"		=>	"task_statuses",
						"foreignKey"	=>	"pickup_status"
					),
					"AssemblingStatus"	=>	array(
						"className"		=>	"task_statuses",
						"foreignKey"	=>	"assembly_status"
					),
					"Customer"	=>	array(
						"className"		=>	"users",
						"foreignKey"	=>	"customer_id"
					),		
				),
				"hasOne"	=>	array(
					"Images"	=>	array(
						"className"		=>	"Content",
						"foreignKey"	=>	"model_id",
						"conditions"	=>	array(
							"Images.model"	=>	"Order",
							"Images.type"	=>	"maxwidth"
						)
					)
				)
			),false);
			
			
			
			// $this->Order->OrderProduct->bindModel(array(
			// 	"belongsTo"	=>	array(
			// 		"Product"	=>	array(
			// 			"fields"	=>	array(
			// 				"Product.name",
			// 				"Product.code"
			// 			)
			// 		)
			// 	)
			// ),false);

			//FIND PRODUCT
			$this->loadModel("OrderProduct");
			$OrderProduct		=	$this->OrderProduct->find("all",array(
										"conditions"	=>	array(
											"OrderProduct.order_id"	=>	$order_id
										),
										"joins"	=>	array(
											array(
												"table"			=>	"products",
												"alias"			=>	"Product",
												'type'			=> 'LEFT',
												"conditions"	=>	array(
													"OrderProduct.product_id	=	Product.id"
												)
											),
											array(
												"table"			=>	"product_images",
												"alias"			=>	"ProductImage",
												'type'			=> 'LEFT',
												"conditions"	=>	array(
													"ProductImage.product_id	=	Product.id"
												)
											),
											array(
												"table"			=>	"contents",
												"alias"			=>	"Image",
												'type'			=> 'LEFT',
												"conditions"	=>	array(
													"
															Image.model_id	=	ProductImage.id
														AND
															Image.model		=	'ProductImage'
														AND
															Image.type		=	'maxwidth'
													"
												)
											)
										),
										"fields"	=>	array(
											"OrderProduct.*",
											"OrderProduct.description",
											"Product.name",
											"Product.code",
											"ProductImage.id",
											"Image.host",
											"Image.url",
											"Image.modified"
										)
									));			
			
			$this->Order->Customer->virtualFields = array(
				"fullname"		=> "CONCAT(Customer.firstname,' ',Customer.lastname)",
			);
							
			$data			=	$this->Order->find("first",array(
									"conditions"	=>	array(
										"Order.id"	=>	$order_id
									),
									"fields"		=>	array(
										"Order.*",
										"DeliveryStatus.name_customer",
										"Customer.id",
										"Customer.firstname",
										"Customer.lastname",
										"Customer.email"
									),
									"recursive"		=>	3
								));
			$data['Product']	=	!empty($OrderProduct) ? $OrderProduct : array();
								
			$driver			=	array();
			$technician		=	array();
			
			if(!empty($data))
			{
				$message		=	"OK";
				$this->loadModel("TaskAssign");
				
				$joins			=	array(
										array(
											"table"			=>	"tasks",
											"alias"			=>	"Task",
											'type'			 => 'LEFT',
											"conditions"	=>	array(
												"TaskAssign.task_id		=	Task.id"
											)
										),
										array(
											"table"			=>	"users",
											"alias"			=>	"Driver",
											'type'			 => 'LEFT',
											"conditions"	=>	array(
												"Driver.id		=	TaskAssign.employee_id"
											)
										),
										array(
											"table"			=>	"contents",
											"alias"			=>	"Image",
											'type'			 => 'LEFT',
											"conditions"	=>	array(
												"
													Image.model_id	=	Driver.id
												AND
													Image.type	=	'maxwidth'
												AND
													Image.model	=	'User'	
												"		
											)
										),
										array(
											"table"			=>	"ratings",
											"alias"			=>	"Rating",
											'type'			 => 'LEFT',
											"conditions"	=>	array(
												"
														Rating.employee_id		=	TaskAssign.employee_id
													AND
														Rating.task_id			=	TaskAssign.task_id
												"
											)
										)
									);
									
				$driver			=	$this->TaskAssign->find("first",array(
										"conditions"	=>	array(
											"TaskAssign.order_id"	=>	$order_id,
											"Task.task_type_id"		=>	"1"
										),
										"joins"		=>	$joins,
										"order"		=>	"TaskAssign.modified asc",
										"fields"	=>	array(
											"Task.id",
											"Task.vehicle_no",
											"Driver.id",
											"Driver.firstname",
											"Driver.lastname",
											"Image.*",
											"Rating.id"
										)
									));
									
				$technician		=	$this->TaskAssign->find("all",array(
										"conditions"	=>	array(
											"TaskAssign.order_id"	=>	$order_id,
											"Task.task_type_id"		=>	"2"
										),
										"joins"		=>	$joins,
										"order"		=>	"TaskAssign.modified asc",
										"fields"	=>	array(
											"Task.id",
											"Task.vehicle_no",
											"Driver.id",
											"Driver.firstname",
											"Driver.lastname",
											"Image.*",
											"Rating.id"
										)
									));
									
				$this->TaskHistory->bindModel(array(
					"belongsTo"	=>	array(
						"Task"
					)
				),false);
				
				$technicianWhoRun	=	$this->TaskHistory->find("first",array(
											"conditions"	=>	array(
												"TaskHistory.order_id"			=>	$order_id,
												"Task.task_type_id"				=>	"2",
												"TaskHistory.status"			=>	"5"
											),
											"order"			=>	"TaskHistory.id"
										));
				//pr($technicianWhoRun);
			}
			else
			{
				$status			=	false;
				$message		=	"Information not found!";
			}
		}
		
		
		$out	=	array(
						"status"			=>	$status,
						"message"			=>	$message,
						"data"				=>	$data,
						"driver"			=>	$driver,
						"technician"		=>	$technician,
						"run_tech"			=>	$technicianWhoRun,
						"code"				=>	$code,
						"request"			=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function DetailTask()
	{
		$status				=	true;
		$message			=	ERR_02;
		$code				=	"02";
		$data				=	array();
		$order_id			=	NULL;
		$driver				=	array();
		
		if(isset($_REQUEST['task_id']))
			$task_id	=	$_REQUEST['task_id'];
		
		if(isset($_REQUEST['user_id']))
			$user_id	=	$_REQUEST['user_id'];
		
		//CHECK USER ACCESS
		$this->loadModel("User");
		$checkUser	=	$this->User->find("first",array(
							"conditions"		=>	array(
								"User.id"		=>	$user_id,
								"User.status"	=>	"1"
							)
						));
						
		if(empty($checkUser))
		{
			$status			=	false;
			$message		=	"You don't have priveledges";
		}
		else
		{
			$this->loadModel("Task");
			$joins			=	array(
									array(
										"table"			=>	"orders",
										"alias"			=>	"Order",
										'type'			 => 'LEFT',
										"conditions"	=>	array(
											"Task.order_id	=	Order.id"
										)
									),
									array(
										"table"			=>	"task_statuses",
										"alias"			=>	"TaskStatus",
										'type'			 => 'LEFT',
										"conditions"	=>	array(
											"Task.status	=	TaskStatus.id"
										)
									),
									array(
										"table"			=>	"users",
										"alias"			=>	"Customer",
										'type'			 => 'LEFT',
										"conditions"	=>	array(
											"Order.customer_id	=	Customer.id"
										)
									)
								);
								
			$data			=	$this->Task->find("first",array(
									"conditions"	=>	array(
										"Task.id"	=>	$task_id
									),
									"joins"			=>	$joins,
									"fields"		=>	array(
										"Task.*",
										"TaskStatus.*",
										"Order.*",
										"Customer.id",
										"Customer.firstname",
										"Customer.lastname",
										"Customer.email"
									)
								));
								
			if(!empty($data))
			{
				$message		=	"OK";
				
				//FIND ASSIGNMENT
				$this->loadModel("TaskAssign");
				$this->TaskAssign->bindModel(array(
					"belongsTo"	=>	array(
						"Employee"	=>	array(
							"className"		=>	"User",
							"foreignKey"	=>	"employee_id"
						)
					)
				));
				
				$findAssignment	=	$this->TaskAssign->find("all",array(
										"conditions"	=>	array(
											"TaskAssign.task_id"	=>	$task_id
										),
										"order"			=>	array(
											"TaskAssign.id"	=>	"asc"
										),
										"fields"		=>	array(
											"TaskAssign.*",
											"Employee.id",
											"Employee.firstname",
											"Employee.lastname"
										)
									));
								
				$assign				=	!empty($findAssignment) ? $findAssignment : array();
				$data['TaskAssign']	=	$assign;
				
				
				//FIND PRODUCT
				$this->loadModel("OrderProduct");
				$OrderProduct		=	$this->OrderProduct->find("all",array(
											"conditions"	=>	array(
												"OrderProduct.order_id"	=>	$data["Order"]["id"]
											),
											"joins"	=>	array(
												array(
													"table"			=>	"products",
													"alias"			=>	"Product",
													'type'			=> 'LEFT',
													"conditions"	=>	array(
														"OrderProduct.product_id	=	Product.id"
													)
												),
												array(
													"table"			=>	"product_images",
													"alias"			=>	"ProductImage",
													'type'			=> 'LEFT',
													"conditions"	=>	array(
														"ProductImage.product_id	=	Product.id"
													)
												),
												array(
													"table"			=>	"contents",
													"alias"			=>	"Image",
													'type'			=> 'LEFT',
													"conditions"	=>	array(
														"
																Image.model_id	=	ProductImage.id
															AND
																Image.model		=	'ProductImage'
															AND
																Image.type		=	'maxwidth'
														"
													)
												)
											),
											"fields"	=>	array(
												"OrderProduct.qty",
												"OrderProduct.description",
												"Product.name",
												"Product.code",
												"ProductImage.id",
												"Image.host",
												"Image.url",
												"Image.modified"
											)
										));
				$data['Product']	=	!empty($OrderProduct) ? $OrderProduct : array();
										
				
				$driver				=	$this->User->find("all",array(
											"conditions"	=>	array(
												"User.status"	=>	"1",
												"User.aro_id"	=>	"6"
											),
											"order"		=>	"User.firstname ASC",
											"fields"	=>	array(
												"User.id",
												"User.firstname",
												"User.lastname",
												"User.email"
											)
										));
										
				$teknisi			=	$this->User->find("all",array(
											"conditions"	=>	array(
												"User.status"	=>	"1",
												"User.aro_id"	=>	"5"
											),
											"order"		=>	"User.firstname ASC",
											"fields"	=>	array(
												"User.id",
												"User.firstname",
												"User.lastname",
												"User.email"
											)
										));
			}
			else
			{
				$status			=	false;
				$message		=	"Information not found!";
			}
		}
		
		
		$out	=	array(
						"status"		=>	$status,
						"message"		=>	$message,
						"data"			=>	$data,
						"driver"		=>	$driver,
						"technisian"	=>	$teknisi,
						"code"			=>	$code,
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function AssignDriver()
	{
		$status			=	false;
		$message		=	ERR_04;
		$code			=	"04";
		$data			=	array();
        $checkinDetail  =   array();

		$request["TaskAssign"]["employee_id"]	=	empty($_REQUEST["employee_id"]) ? "" : $_REQUEST["employee_id"];
		$request["TaskAssign"]["task_id"]		=	empty($_REQUEST["order_id"]) ? "" : $_REQUEST["task_id"];
		$request["TaskAssign"]["order_id"]		=	empty($_REQUEST["order_id"]) ? "" : $_REQUEST["order_id"];
		$request["TaskAssign"]["vehicle_no"]	=	empty($_REQUEST["vehicle_no"]) ? "" : $_REQUEST["vehicle_no"];
		$request["TaskAssign"]["user_id"]		=	empty($_REQUEST["user_id"]) ? "" : $_REQUEST["user_id"];

		$this->loadModel('TaskAssign');
		$this->TaskAssign->set($request);
		$this->TaskAssign->ValidateAssignDriver();


		$error									=	$this->TaskAssign->InvalidFields();
		
		if(empty($error))
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			
			$this->TaskAssign->create();
			$save		=	$this->TaskAssign->save($request,array("validate"=>false));
			$data		=	$this->TaskAssign->find("first",array(
								"conditions"	=>	array(
									"TaskAssign.id"	=>	$this->TaskAssign->id
								)
							));
							
			//UPDATE TASK
			$this->loadModel("Task");
			$this->Task->updateAll(
				array(
					"status"	=>	2
				),
				array(
					"Task.id"	=>	$request["TaskAssign"]["task_id"]
				)
			);

			//ADD ORDER HISTORY
			$this->loadModel("OrderHistory");
			$request["OrderHistory"]["user_id"]			=	$_REQUEST["employee_id"];
			$request["OrderHistory"]["order_id"]		=	$_REQUEST["order_id"];
			$request["OrderHistory"]["description"]		=	"Sudah dapat driver";
			$request["OrderHistory"]["status"]			=	"1";
			$this->OrderHistory->saveAll($request,array("validate"=>false));
			
			//UPDATE ORDER
			$this->loadModel("Order");
			$this->Order->updateAll(
				array(
					'delivery_status'	=>	2
				),
				array(
					'Order.id'			=>	$request["TaskAssign"]["order_id"]
				)
			);
		}
		else
		{
			$status		=	false;
			foreach($error as $k => $v)
			{
				$message	=	$v[0];
				break;
			}
			$code		=	"03";
			$data		=	null;
		}

		$out			=	array(
			"status"				=>	$status,
			"message"				=>	$message,
			"data"					=>	$data,
			"code"					=>	$code,
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);


		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function CancelDeliveryOrder()
	{
		$status			=	false;
		$message		=	ERR_04;
		$code			=	"04";
		$data			=	array();
		
		$request["TaskAssign"]["task_id"]		=	$taskId		=	empty($_REQUEST["order_id"]) ? "" : $_REQUEST["task_id"];
		$request["TaskAssign"]["order_id"]		=	$orderId	=	empty($_REQUEST["order_id"]) ? "" : $_REQUEST["order_id"];
		$request["TaskAssign"]["user_id"]		=	$userId		=	empty($_REQUEST["user_id"]) ? "" : $_REQUEST["user_id"];
		$reason									=	empty($_REQUEST["reson"]) ? "Cancelled by Head of Inventory" : $_REQUEST["reson"];
		
		$this->loadModel("TaskAssign");
		$this->loadModel("TaskHistory");
		$this->loadModel("NotificationGroup");
		$this->loadModel("Notification");
		$this->loadModel("User");
		
		
		$this->loadModel("Order");
		$this->loadModel("Task");
		$this->TaskAssign->set($request);
		$this->TaskAssign->ValidateCancelDeliveryOrder();
		$error									=	$this->TaskAssign->InvalidFields();
		
		if(empty($error))
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			
			
			//DETAIL ORDER
			$detailOrder	=	$this->Order->find("first",array(
									"conditions"	=>	array(
										"Order.id"	=>	$request["TaskAssign"]["order_id"]
									)
								));
								
			//DETAIL TASK
			$detailTask	=	$this->Task->find("first",array(
								"conditions"	=>	array(
									"Task.id"	=>	$request["TaskAssign"]["task_id"]
								)
							));
								
			//======= UPDATE ORDER DELIVERY STATUS ===========//
			/*
			* Pertama delivery status order nya di balikin dulu jadi accepted(delivery_status=3)
			*
			*
			*/
			if($detailOrder["Order"]["is_assembling"] == "1")
			{
				$this->Order->updateAll(
					array(
						"assembly_status"		=>	"8"
					),
					array(
						"Order.id"				=>	$orderId
					)
				);
			} else {
				$this->Order->updateAll(
					array(
						"delivery_status"		=>	"8"
					),
					array(
						"Order.id"				=>	$orderId
					)
				);
			}
			
			//======= UPDATE ORDER DELIVERY STATUS ===========//
			
			//======= UPDATE TASK STATUS ===========//
			$this->Task->updateAll(
				array(
					"status"				=>	"8"
				),
				array(
					"Task.order_id"			=>	$request["TaskAssign"]["order_id"]
				)
			);
			//======= UPDATE TASK STATUS ===========//

			//ORDER HISTORY
			$this->loadModel("OrderHistory");
			$request["OrderHistory"]["user_id"]			=	$userId;
			$request["OrderHistory"]["order_id"]		=	$orderId;
			$request["OrderHistory"]["description"]		=	"Pesanan dibatalkan";
			$request["OrderHistory"]["status"]			=	"1";
			$saveHistory = $this->OrderHistory->saveAll($request,array("validate"=>false));
			if($saveHistory){
				$request["OrderHistory"]["status"]			=	"2";
				$saveHistory = $this->OrderHistory->saveAll($request,array("validate"=>false));
			}
			//======= DELETE TASK ASSIGN ===========//
			$this->TaskAssign->deleteAll(array(
				"TaskAssign.order_id"		=>	$request["TaskAssign"]["order_id"]
			));
			//======= DELETE TASK ASSIGN ===========//
			
			//======= UPDATE TASK HISTORY FOR DELIVERY ===========//
			$fAssign	=	$this->TaskHistory->find("all",array(
								"conditions"	=>	array(
									"TaskHistory.task_id"			=>	$taskId,
									"TaskHistory.order_id"			=>	$orderId,
									"TaskHistory.status"			=>	array("3","5"),
									"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = ".$taskId." and order_id=".$orderId." and employee_id = TaskHistory.employee_id)"
								),
								"group"			=>	array(
									"TaskHistory.employee_id"
								),
								"fields"	=>	array(
									"TaskHistory.employee_id"
								)
							));
								
			foreach($fAssign as $fAssign)
			{
				$this->TaskHistory->create();
				$save	=	$this->TaskHistory->saveAll(
					array(
						"task_id"		=>	$request["TaskAssign"]["task_id"],
						"order_id"		=>	$request["TaskAssign"]["order_id"],
						"employee_id"	=>	$fAssign["TaskHistory"]["employee_id"],
						"reason"		=>	$reason,
						"status"		=>	"8"
					),
					array(
						"validate"		=>	false
					)
				);
				$listCancelAssignUserId[]	=	$fAssign["TaskHistory"]["employee_id"];
			}
			//======= UPDATE TASK HISTORY FOR DELIVERY ===========//
			
			
			//======= UPDATE TASK HISTORY FOR ASSEMBLY ===========//
			if($detailOrder["Order"]["is_assembling"] == "1")
			{
				$fTask		=	$this->Task->find("first",array(
									"conditions"	=>	array(
										"Task.order_id"			=>	$orderId,
										"Task.task_type_id"		=>	"2"
									)
								));
								
				if(!empty($fTask))
				{
					$taskIdAssembly	=	$fTask["Task"]["id"];
					
					
					$fAssign		=	$this->TaskHistory->find("all",array(
											"conditions"	=>	array(
												"TaskHistory.order_id"			=>	$orderId,
												"TaskHistory.status"			=>	array("2","3","5"),
												"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = ".$taskIdAssembly." and order_id=".$orderId." and employee_id = TaskHistory.employee_id)"
											),
											"group"			=>	array(
												"TaskHistory.employee_id"
											),
											"fields"	=>	array(
												"TaskHistory.employee_id"
											)
										));
										
					foreach($fAssign as $fAssign)
					{
						$this->TaskHistory->create();
						$save	=	$this->TaskHistory->saveAll(
							array(
								"task_id"		=>	$request["TaskAssign"]["task_id"],
								"order_id"		=>	$request["TaskAssign"]["order_id"],
								"employee_id"	=>	$fAssign["TaskHistory"]["employee_id"],
								"reason"		=>	$reason,
								"status"		=>	"8"
							),
							array(
								"validate"		=>	false
							)
						);
						$listCancelAssignUserId[]	=	$fAssign["TaskHistory"]["employee_id"];
					}
				}
			}
			//======= UPDATE TASK HISTORY FOR ASSEMBLY ===========//
			
			//=========== SAVE NOTIFICATION ================//
			$detailCancelAssignUserId	=	$this->User->find("list",array(
												"conditions"	=>	array(
													"User.id"	=>	$listCancelAssignUserId
												),
												"fields"		=>	array(
													"User.id",
													"User.gcm_id"
												)
											));
			
			if(!empty($detailCancelAssignUserId))
			{
				$arrGcmId		=	array();
				$title			=	'INDORACK';
				$message    	=	"Job Cancelled ".$detailOrder['Order']['delivery_no'];
				$description   	=	"PO No. : ".$detailOrder['Order']['order_no']."<br/>Delivery No. : ".$detailOrder['Order']['delivery_no']."<br/>To : ".$detailOrder["Order"]["address"]."<br/>Delivery Date : ".date("d M Y H:i",strtotime($detailOrder['Order']['delivery_date']));
				
				$created		=	date("Y-m-d H:i:s");
				
				//CREATE NOTIFICATION GROUP
				$this->NotificationGroup->create();
				$this->NotificationGroup->saveAll(
					array(
						"created"	=>	$created
					),
					array(
						"validate"	=>	false
					)
				);
				$notificationGroupId	=	$this->NotificationGroup->id;
				
				foreach($detailCancelAssignUserId as $user_id => $gcm_id)
				{
					$this->Notification->create();
					$Notif["Notification"]["user_id"]					=	$user_id;
					$Notif["Notification"]["gcm_id"]					=	empty($gcm_id) ? NULL : $gcm_id;
					$Notif["Notification"]["notification_group_id"] 	=	$notificationGroupId;
					$Notif["Notification"]["order_id"]					=	$detailOrder['Order']['id'];
					$Notif["Notification"]["title"]						=	$title;
					$Notif["Notification"]["title"]						=	$title;
					$Notif["Notification"]["android_class_name"]		=	($detailTask['Task']['task_type_id'] == "1") ? 'DashboardDriver' : 'DashboardTeknisiActivity';
					$Notif["Notification"]["message"]					=	$message;
					$Notif["Notification"]["description"]				=	$description;
					$Notif["Notification"]["created"]					=	$created;
					
					if(!empty($gcm_id))
						$arrGcmId[]										=	$gcm_id;
					$this->Notification->save($Notif,array("validate"=>false));
				}
				
				if(!empty($arrGcmId))
				{
					$res 						=	array();
					$res['data']['title'] 		=	$title;
					$res['data']['message'] 	=	$message;
					$res['data']['class_name'] 	=	($detailTask['Task']['task_type_id'] == "1") ? 'DashboardDriver' : 'DashboardTeknisiActivity';
					$res['data']['params'] 		=	array();
					$res['data']['created'] 	=	$created;
					$res['data']['notification_group_id'] 	=	$notificationGroupId;
					$fields = array(
						"registration_ids" 		=>	$arrGcmId,
						"data" 					=>	$res,
						"priority"				=>	"high",
						"time_to_live"			=>	2419200
					);
					$push	=	$this->General->sendPushNotification($fields);
				}
			}
			//=========== SAVE NOTIFICATION ================//
		}
		else
		{
			$status		=	false;
			foreach($error as $k => $v)
			{
				$message	=	$v[0];
				break;
			}
			$code		=	"03";
			$data		=	null;
		}
		
		$out			=	array(
			"status"				=>	$status,
			"message"				=>	$message,
			"data"					=>	$data,
			"code"					=>	$code,
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);


		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	
	function AssignTechnisian()
	{
		$status			=	false;
		$message		=	ERR_04;
		$code			=	"04";
		$data			=	array();
        $checkinDetail  =   array();

		$request["TaskAssign"]["task_id"]		=	empty($_REQUEST["order_id"]) ? "" : $_REQUEST["task_id"];
		$request["TaskAssign"]["order_id"]		=	empty($_REQUEST["order_id"]) ? "" : $_REQUEST["order_id"];
		$request["TaskAssign"]["user_id"]		=	empty($_REQUEST["user_id"]) ? "" : $_REQUEST["user_id"];
		$request["TaskAssign"]["vehicle_no"]	=	empty($_REQUEST["vehicle_no"]) ? "" : $_REQUEST["vehicle_no"];
		
		$request["TaskAssign"]["technisian_id"] =	$technisian_id = empty($_REQUEST["technisian_id"]) ? "" : $_REQUEST["technisian_id"];

		$this->loadModel('User');
		$this->loadModel("Order");
		$this->loadModel('TaskHistory');
		$this->loadModel('TaskAssign');
		$this->loadModel("Task");
		$this->loadModel("Notification");
		$this->loadModel("NotificationGroup");
		
		$this->TaskAssign->set($request);
		$this->TaskAssign->ValidateAssignTechnisian();
		$error									=	$this->TaskAssign->InvalidFields();
		
		if(empty($error))
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			
			//DETAIL TASK
			$detailTask		=	$this->Task->find("first",array(
									"conditions"	=>	array(
										"Task.id"	=>	$request["TaskAssign"]["task_id"]
									)
								));
							
			//DETAIL ORDER
			$detailOrder	=	$this->Order->find("first",array(
									"conditions"	=>	array(
										"Order.id"	=>	$request["TaskAssign"]["order_id"]
									)
								));
							
			//UPDATE VEHICLE NO
			if($detailTask["Task"]["task_type_id"] == "1")
			{
				$this->Task->updateAll(
					array(
						"vehicle_no"	=>	"'".$request["TaskAssign"]["vehicle_no"]."'"
					),
					array(
						"Task.id"		=>	$request["TaskAssign"]["task_id"]
					)
				);
			}
			
			if(is_array($technisian_id) && $technisian_id > 0)
			{
				$fAssign	=	$this->TaskHistory->find("all",array(
									"conditions"	=>	array(
										"TaskHistory.task_id"			=>	$request["TaskAssign"]["task_id"],
										"TaskHistory.order_id"			=>	$request["TaskAssign"]["order_id"],
										"TaskHistory.status"			=>	array("2","3"),
										"NOT"	=>	array(
											"TaskHistory.employee_id"	=>	$technisian_id
										),
										"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = ".$request["TaskAssign"]["task_id"]." and order_id=".$request["TaskAssign"]["order_id"]." and employee_id = TaskHistory.employee_id)"
									),
									"group"			=>	array(
										"TaskHistory.employee_id"
									),
									"fields"	=>	array(
										"TaskHistory.employee_id"
									)
								));
				
				$listCancelAssignUserId	=	array();
				foreach($fAssign as $fAssign)
				{
					$this->TaskHistory->create();
					$save	=	$this->TaskHistory->saveAll(
						array(
							"task_id"		=>	$request["TaskAssign"]["task_id"],
							"order_id"		=>	$request["TaskAssign"]["order_id"],
							"employee_id"	=>	$fAssign["TaskHistory"]["employee_id"],
							"reason"		=>	"Cancelled by Head of Inventory",
							"status"		=>	"8"
						),
						array(
							"validate"		=>	false
						)
					);
					
					//if($save)
					//{
						$listCancelAssignUserId[]	=	$fAssign["TaskHistory"]["employee_id"];
					//}
				}
				
				//=========== SAVE NOTIFICATION ================//
				$detailCancelAssignUserId	=	$this->User->find("list",array(
													"conditions"	=>	array(
														"User.id"	=>	$listCancelAssignUserId
													),
													"fields"		=>	array(
														"User.id",
														"User.gcm_id"
													)
												));
				
				if(!empty($detailCancelAssignUserId))
				{
					$arrGcmId		=	array();
					$title			=	'INDORACK';
					$message    	=	"Job Cancelled ".$detailOrder['Order']['delivery_no'];
					$description   	=	"PO No. : ".$detailOrder['Order']['order_no']."<br/>Delivery No. : ".$detailOrder['Order']['delivery_no']."<br/>To : ".$detailOrder["Order"]["address"]."<br/>Delivery Date : ".date("d M Y H:i",strtotime($detailOrder['Order']['delivery_date']));
					
					$created		=	date("Y-m-d H:i:s");
					
					//CREATE NOTIFICATION GROUP
					$this->NotificationGroup->create();
					$this->NotificationGroup->saveAll(
						array(
							"created"	=>	$created
						),
						array(
							"validate"	=>	false
						)
					);
					$notificationGroupId	=	$this->NotificationGroup->id;
					
					foreach($detailCancelAssignUserId as $user_id => $gcm_id)
					{
						$this->Notification->create();
						$Notif["Notification"]["user_id"]					=	$user_id;
						$Notif["Notification"]["gcm_id"]					=	empty($gcm_id) ? NULL : $gcm_id;
						$Notif["Notification"]["notification_group_id"] 	=	$notificationGroupId;
						$Notif["Notification"]["order_id"]					=	$detailOrder['Order']['id'];
						$Notif["Notification"]["title"]						=	$title;
						$Notif["Notification"]["title"]						=	$title;
						$Notif["Notification"]["android_class_name"]		=	($detailTask['Task']['task_type_id'] == "1") ? 'DashboardDriver' : 'DashboardTeknisiActivity';
						$Notif["Notification"]["message"]					=	$message;
						$Notif["Notification"]["description"]				=	$description;
						$Notif["Notification"]["created"]					=	$created;
						
						if(!empty($gcm_id))
							$arrGcmId[]										=	$gcm_id;
						$this->Notification->save($Notif,array("validate"=>false));
					}
					
					if(!empty($arrGcmId))
					{
						$res 						=	array();
						$res['data']['title'] 		=	$title;
						$res['data']['message'] 	=	$message;
						$res['data']['class_name'] 	=	($detailTask['Task']['task_type_id'] == "1") ? 'DashboardDriver' : 'DashboardTeknisiActivity';
						$res['data']['params'] 		=	array();
						$res['data']['created'] 	=	$created;
						$res['data']['notification_group_id'] 	=	$notificationGroupId;
						$fields = array(
							"registration_ids" 		=>	$arrGcmId,
							"data" 					=>	$res,
							"priority"				=>	"high",
							"time_to_live"			=>	2419200
						);
						$push	=	$this->General->sendPushNotification($fields);
					}
				}
				//=========== SAVE NOTIFICATION ================//
				
				$this->TaskAssign->deleteAll(array(
					"task_id"	=>	$request["TaskAssign"]["task_id"],
					"order_id"	=>	$request["TaskAssign"]["order_id"],
					"NOT"		=>	array(
						"employee_id"	=>	$technisian_id
					),
				));
				
				$listAssignUserId	=	array();
				foreach($technisian_id as $employeeId)
				{
					if(empty($employeeId))
						continue;
						
					//SAVE TASK ASSIGN
					$this->TaskAssign->create();
					$request["TaskAssign"]["employee_id"]		=	$employeeId;
					$request["TaskAssign"]["status"]			=	"2";
					$request["TaskAssign"]["vehicle_no"]		=	NULL;
					$save										=	$this->TaskAssign->save($request,array("validate"=>false));
					
					//SAVE TASK HISTORY
					$this->TaskHistory->create();
					$TaskHistory["TaskHistory"]["employee_id"]	=	$employeeId;
					$TaskHistory["TaskHistory"]["task_id"]		=	$request["TaskAssign"]["task_id"];
					$TaskHistory["TaskHistory"]["order_id"]		=	$request["TaskAssign"]["order_id"];
					$TaskHistory["TaskHistory"]["status"]		=	2;
					$save										=	$this->TaskHistory->save($TaskHistory,array("validate"=>false));
					//if($save)
					//{
						$listAssignUserId[]	=	$employeeId;
					//}
				}
				
				//=========== SAVE NOTIFICATION ================//
				$detailAssignUserId	=	$this->User->find("list",array(
											"conditions"	=>	array(
												"User.id"	=>	$listAssignUserId
											),
											"fields"		=>	array(
												"User.id",
												"User.gcm_id"
											)
										));
				
				if(!empty($detailAssignUserId))
				{
					$arrGcmId		=	array();
					$title			=	'INDORACK';
					$message    	=	"New job request ".$detailOrder['Order']['delivery_no'];
					$description   	=	"PO No. : ".$detailOrder['Order']['order_no']."<br/>Delivery No. : ".$detailOrder['Order']['delivery_no']."<br/>To : ".$detailOrder["Order"]["address"]."<br/>Delivery Date : ".date("d M Y H:i",strtotime($detailOrder['Order']['delivery_date']));
					
					$created		=	date("Y-m-d H:i:s");
					
					//CREATE NOTIFICATION GROUP
					$this->loadModel("NotificationGroup");
					$this->NotificationGroup->create();
					$this->NotificationGroup->saveAll(
						array(
							"created"	=>	$created
						),
						array(
							"validate"	=>	false
						)
					);
					$notificationGroupId	=	$this->NotificationGroup->id;
					
					foreach($detailAssignUserId as $user_id =>$gcm_id)
					{
						$this->Notification->create();
						$Notif["Notification"]["user_id"]					=	$user_id;
						$Notif["Notification"]["gcm_id"]					=	empty($gcm_id) ? NULL : $gcm_id;
						$Notif["Notification"]["notification_group_id"] 	=	$notificationGroupId;
						$Notif["Notification"]["order_id"]					=	$detailOrder['Order']['id'];
						$Notif["Notification"]["title"]						=	$title;
						$Notif["Notification"]["title"]						=	$title;
						$Notif["Notification"]["android_class_name"]		=	($detailTask['Task']['task_type_id'] == "1") ? 'DashboardDriver' : 'DashboardTeknisiActivity';
						$Notif["Notification"]["message"]					=	$message;
						$Notif["Notification"]["description"]				=	$description;
						$Notif["Notification"]["created"]					=	$created;
						
						if(!empty($gcm_id))
							$arrGcmId[]										=	$gcm_id;
						$this->Notification->save($Notif,array("validate"=>false));
					}
					
					if(!empty($arrGcmId))
					{
						$res 						=	array();
						$res['data']['title'] 		=	$title;
						$res['data']['message'] 	=	$message;
						$res['data']['class_name'] 	=	($detailTask['Task']['task_type_id'] == "1") ? 'DashboardDriver' : 'DashboardTeknisiActivity';
						$res['data']['params'] 		=	array();
						$res['data']['created'] 	=	$created;
						$res['data']['notification_group_id'] 	=	$notificationGroupId;
						$fields = array(
							"registration_ids" 		=>	$arrGcmId,
							"data" 					=>	$res,
							"priority"				=>	"high",
							"time_to_live"			=>	2419200
						);
						$push	=	$this->General->sendPushNotification($fields);
					}
				}
				//=========== SAVE NOTIFICATION ================//
				
				
			}//if technician_id >0

			if($detailTask["Task"]["status"] == "1")
			{
				//UPDATE TASK
				$this->Task->updateAll(
					array(
						"status"			=>	2
					),
					array(
						"Task.id"			=>	$request["TaskAssign"]["task_id"]
					)
				);
				
				//UPDATE ORDER
				$this->loadModel("Order");
				//ORDER HISTORY
				$this->loadModel("OrderHistory");
				$request["OrderHistory"]["user_id"]			=	$_REQUEST["user_id"];
				$request["OrderHistory"]["order_id"]		=	$_REQUEST["order_id"];
	
				if($detailTask['Task']['task_type_id']=="2")
				{
					$this->Order->updateAll(
						array(
							'assembly_status'	=>	2
						),
						array(
							'Order.id'			=>	$request["TaskAssign"]["order_id"]
						)
					);

					$request["OrderHistory"]["description"]		=	"Sedang menyiapkan Teknisi";
					$request["OrderHistory"]["status"]			=	"2";
					$saveHistory = $this->OrderHistory->saveAll($request,array("validate"=>false));
				}
				else if($detailTask['Task']['task_type_id']=="1")
				{
					$this->Order->updateAll(
						array(
							'delivery_status'	=>	2
						),
						array(
							'Order.id'			=>	$request["TaskAssign"]["order_id"]
						)
					);

					$request["OrderHistory"]["description"]		=	"Barang telah disiapkan";
					$request["OrderHistory"]["status"]			=	"1";
					$saveHistory = $this->OrderHistory->saveAll($request,array("validate"=>false));
				}


			}
			else
			{
				$checkTaskAssign	=	$this->TaskAssign->find("all",array(
											"conditions"	=>	array(
												"TaskAssign.task_id"	=>	$request["TaskAssign"]["task_id"]
											),
											"group"	=>	"TaskAssign.status"
										));
										
				if(count($checkTaskAssign) == 1)
				{
					//UPDATE TASK
					$this->Task->updateAll(
						array(
							"status"			=>	$checkTaskAssign[0]["TaskAssign"]["status"]
						),
						array(
							"Task.id"			=>	$request["TaskAssign"]["task_id"]
						)
					);
					
					//UPDATE ORDER
					$this->loadModel("Order");
					
					if($detailTask['Task']['task_type_id']=="2")
					{
						$this->Order->updateAll(
							array(
								'assembly_status'	=>	$checkTaskAssign[0]["TaskAssign"]["status"]
							),
							array(
								'Order.id'			=>	$request["TaskAssign"]["order_id"]
							)
						);
					}
					else if($detailTask['Task']['task_type_id']=="1")
					{
						$this->Order->updateAll(
							array(
								'delivery_status'	=>	$checkTaskAssign[0]["TaskAssign"]["status"]
							),
							array(
								'Order.id'			=>	$request["TaskAssign"]["order_id"]
							)
						);
					}
					
				}
			}
		}
		else
		{
			$status		=	false;
			foreach($error as $k => $v)
			{
				$message	=	$v[0];
				break;
			}
			$code		=	"03";
			$data		=	null;
		}

		$out			=	array(
			"status"				=>	$status,
			"message"				=>	$message,
			"data"					=>	$data,
			"code"					=>	$code,
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);


		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	
	function PickupFinish()
	{
		$status					=	false;
		$message				=	ERR_04;
		$code					=	"04";
		$data					=	array();
		
		$request["Order"]["id"]				=	$orderId		=	empty($_REQUEST["order_id"]) ? "" : $_REQUEST["order_id"];
		
		$request["Order"]["employee_id"]	=	$employeeId		=	empty($_REQUEST["employee_id"]) ? "" : $_REQUEST["employee_id"];
		
		$request["Order"]["receiver_name"]	=	$receiverName	=	empty($_REQUEST["receiver_name"]) ? "" : $_REQUEST["receiver_name"];
		
		$request["Order"]["receiver_phone"]	=	$receiverPhone	=	empty($_REQUEST["receiver_phone"]) ? "" : $_REQUEST["receiver_phone"];
		
		$request["Order"]["images"]			=	isset($_FILES['images']) ? $_FILES['images'] : NULL;
		
		$this->loadModel('Order');
		$this->Order->set($request);
		$this->Order->ValidatePickup();
		$error	=	$this->Order->InvalidFields();
		
		if(empty($error))
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			
			$request["Order"]["pickup_status"]	=	"10";
			$request["Order"]["receiver_date"]	=	date("Y-m-d H:i:s");
			
			
			$this->Order->save($request,array("validate"=>false));
			$ID			=	$this->Order->id;
			

			//ORDER HISTORY
			$this->loadModel("OrderHistory");
			$request["OrderHistory"]["user_id"]			=	$employeeId;
			$request["OrderHistory"]["order_id"]		=	$orderId;
			$request["OrderHistory"]["description"]		=	"Barang sudah diambil";
			$request["OrderHistory"]["status"]			=	"3";
			$saveHistory = $this->OrderHistory->saveAll($request,array("validate"=>false));

			
			//=============== UPLOAD IMAGES =====================//
			if(!empty($_FILES['images']['name']))
			{
				$tmp_name							=	$_FILES['images']["name"];
				$tmp								=	$_FILES['images']["tmp_name"];
				

				$path_tmp							=	ROOT.DS.'app'.DS.'tmp'.DS.'upload'.DS;
					if(!is_dir($path_tmp)) mkdir($path_tmp,0777);

				$ext								=	pathinfo($tmp_name,PATHINFO_EXTENSION);
				$tmp_file_name						=	md5(time());
				$tmp_images1_img					=	$path_tmp.$tmp_file_name.".".$ext;
				$upload 							=	move_uploaded_file($tmp,$tmp_images1_img);
				
				if($upload)
				{
					$mime_type						=	mime_content_type($tmp_images1_img);
					$resize							=	$this->General->ResizeImageContent(
														  $tmp_images1_img,
														  $this->settings["cms_url"],
														  $ID,
														  "Order",
														  "maxwidth",
														  $mime_type,
														  800,
														  300,
														  "resizeMaxWidth"
													  );

				}
				@unlink($tmp_images1_img);
			}
			//=============== UPLOAD IMAGES =====================//
		}
		else
		{
			$status		=	false;
			foreach($error as $k => $v)
			{
				$message	=	$v[0];
				break;
			}
			$code		=	"03";
			$data		=	null;
		}

		$out			=	array(
			"status"				=>	$status,
			"message"				=>	$message,
			"data"					=>	$data,
			"code"					=>	$code,
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	function PickupPrepare()
	{
		$status					=	false;
		$message				=	ERR_04;
		$code					=	"04";
		$data					=	array();
		
		$request["Order"]["id"]				=	$orderId		=	empty($_REQUEST["order_id"]) ? "" : $_REQUEST["order_id"];
		
		$request["Order"]["employee_id"]	=	$employeeId		=	empty($_REQUEST["employee_id"]) ? "" : $_REQUEST["employee_id"];
		
		$this->loadModel('Order');
		$this->Order->set($request);
		$this->Order->ValidatePickup();
		$error	=	$this->Order->InvalidFields();
		
		if(empty($error))
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			
			$request["Order"]["pickup_status"]	=	"9";
			$request["Order"]["receiver_date"]	=	date("Y-m-d H:i:s");
			
			
			$this->Order->save($request,array("validate"=>false));
			$ID			=	$this->Order->id;
			

			//ORDER HISTORY
			$this->loadModel("OrderHistory");
			$request["OrderHistory"]["user_id"]			=	$employeeId;
			$request["OrderHistory"]["order_id"]		=	$orderId;
			$request["OrderHistory"]["description"]		=	"Barang telah disiapkan";
			$request["OrderHistory"]["status"]			=	"3";
			$saveHistory = $this->OrderHistory->saveAll($request,array("validate"=>false));
		}
		else
		{
			$status		=	false;
			foreach($error as $k => $v)
			{
				$message	=	$v[0];
				break;
			}
			$code		=	"03";
			$data		=	null;
		}

		$out			=	array(
			"status"				=>	$status,
			"message"				=>	$message,
			"data"					=>	$data,
			"code"					=>	$code,
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}


	
	function JobResponse()
	{
		$status			=	false;
		$message		=	ERR_04;
		$code			=	"04";
		$data			=	array();
        $checkinDetail  =   array();

		$request["Task"]["id"]			=	$taskId			=	empty($_REQUEST["task_id"]) ? "" : $_REQUEST["task_id"];
		$request["Task"]["employee_id"]	=	$employeeId		=	empty($_REQUEST["employee_id"]) ? "" : $_REQUEST["employee_id"];
		$request["Task"]["status"]		=	$updateStatus	=	empty($_REQUEST["status"]) ? NULL : $_REQUEST["status"];
		$request["Task"]["reason"]		=	$reason			=	empty($_REQUEST["reason"]) ? NULL : $_REQUEST["reason"];
		$reason							=	(isset($_REQUEST["notes"]) && !empty($_REQUEST["notes"]) ) ? $_REQUEST["notes"] : $reason;

		$this->loadModel('User');
		$this->loadModel('Task');
		$this->loadModel('TaskAssign');
		$this->loadModel('TaskHistory');
		$this->loadModel('Notification');
		$this->loadModel('NotificationGroup');
		$this->loadModel('Order');
		
		$this->Task->set($request);
		$this->Task->ValidateResponse();
		$error	=	$this->Task->InvalidFields();
		
		if(empty($error))
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			
			//DETAIL TASK
			$detailTask	=	$this->Task->find("first",array(
								"conditions"	=>	array(
									"Task.id"	=>	$taskId	
								)
							));
							
							
			$orderId	=	$detailTask["Task"]["order_id"];
			
			//DETAIL ORDER
			$detailOrder	=	$this->Order->find("first",array(
									"conditions"	=>	array(
										"Order.id"	=>	$orderId
									)
								));
								
			//DETAIL EMPLOYEE
			$detailEmployee	=	$this->User->find("first",array(
									"conditions"	=>	array(
										"User.id"	=>	$employeeId
									)
								));
								
			//DETAIL CUSTOMER
			$detailCustomer	=	$this->User->find("first",array(
									"conditions"	=>	array(
										"User.id"	=>	$detailOrder["Order"]["customer_id"]
									)
								));
								
								
			//UPDATE TASK ASSIGN
			$taskAssignUpdate	=	array(
										"status"					=>	$updateStatus
									);
									
			if(!is_null($reason))
				$taskAssignUpdate["reason"]	=	"'".$reason."'";
									
			$this->TaskAssign->updateAll(
				$taskAssignUpdate,
				array(
					"TaskAssign.task_id"		=>	$taskId,
					"TaskAssign.employee_id"	=>	$employeeId
				)
			);
			
			//INSERT TASK HISTORY
			$taskHistoryUpdate	=	array(
										"task_id"		=>	$taskId,
										"employee_id"	=>	$employeeId,
										"order_id"		=>	$orderId,
										"status"		=>	$updateStatus,
									);
			if(!is_null($reason))
				$taskHistoryUpdate["reason"]	=	$reason;
				
			$this->TaskHistory->saveAll(
				$taskHistoryUpdate,
				array(
					"validate"	=>	false
				)
			);
			
			//UPDATE TASK
			$checkTaskAssign		=	$this->TaskAssign->find("all",array(
											"conditions"	=>	array(
												"TaskAssign.task_id"	=>	$taskId
											),
											"group"		=>	"TaskAssign.status",
											"order"		=>	"TaskAssign.modified desc"
										));
										
										
			//=========== SAVE NOTIFICATION ================//
			$detailAssignUserId	=	$this->User->find("list",array(
										"conditions"	=>	array(
											"User.aro_id"	=>	4,
											"User.status"	=>	1
										),
										"fields"		=>	array(
											"User.id",
											"User.gcm_id"
										)
									));
			
			if(!empty($detailAssignUserId))
			{
				$arrGcmId		=	array();
				$title			=	'INDORACK';
				$userName		=	$detailEmployee["User"]["firstname"]." ".$detailEmployee["User"]["lastname"];
				$message		=	"";
				
				if($updateStatus == "3")
				{
					$message    	=	$userName." has accepted job ".$detailOrder['Order']['delivery_no'];
				}
				else if($updateStatus == "4")
				{
					$message    	=	$userName." has rejected job ".$detailOrder['Order']['delivery_no'];
				}
				else if($updateStatus == "5")
				{
					if($detailTask["Task"]["task_type_id"] == "1")
						$message    	=	$detailOrder['Order']['delivery_no']." is on deliver progress ";
					else
						$message    	=	$detailOrder['Order']['delivery_no']." is on assembly progress ";
				}
				else if($updateStatus == "6")
				{
					if($detailTask["Task"]["task_type_id"] == "1")
						$message    	=	$detailOrder['Order']['delivery_no']." has completely sent";
					else
						$message    	=	$detailOrder['Order']['delivery_no']." has completely assembled";
				}
				else if($updateStatus == "7")
				{
					if($detailTask["Task"]["task_type_id"] == "1")
						$message    	=	$detailOrder['Order']['delivery_no']." was failed sent";
					else
						$message    	=	$detailOrder['Order']['delivery_no']." was failed assembled";
				}
				
				$description   	=	"PO No. : ".$detailOrder['Order']['order_no']."<br/>Delivery No. : ".$detailOrder['Order']['delivery_no']."<br/>To : ".$detailOrder["Order"]["receiver_name"]."(".$detailOrder["Order"]["address"].")";
				
				$created		=	date("Y-m-d H:i:s");
				
				//CREATE NOTIFICATION GROUP
				$this->loadModel("NotificationGroup");
				$this->NotificationGroup->create();
				$this->NotificationGroup->saveAll(
					array(
						"created"	=>	$created
					),
					array(
						"validate"	=>	false
					)
				);
				$notificationGroupId	=	$this->NotificationGroup->id;
				
				foreach($detailAssignUserId as $user_id =>$gcm_id)
				{
					$this->Notification->create();
					$Notif["Notification"]["user_id"]					=	$user_id;
					$Notif["Notification"]["gcm_id"]					=	empty($gcm_id) ? NULL : $gcm_id;
					$Notif["Notification"]["notification_group_id"] 	=	$notificationGroupId;
					$Notif["Notification"]["order_id"]					=	$detailOrder['Order']['id'];
					$Notif["Notification"]["title"]						=	$title;
					$Notif["Notification"]["params"]					=	json_encode(array(
																				array(
																					"key"	=>	"task_id",
																					"val"	=>	$taskId
																				),
																				array(
																					"key"	=>	"order_id",
																					"val"	=>	$detailOrder["Order"]["id"]
																				)
																			));
					$Notif["Notification"]["android_class_name"]		=	($detailTask['Task']['task_type_id'] == "1") ? 'AssignDriverNewActivity' : 'AssignTechnisian';
					$Notif["Notification"]["message"]					=	$message;
					$Notif["Notification"]["description"]				=	$description;
					$Notif["Notification"]["created"]					=	$created;
					
					if(!empty($gcm_id))
						$arrGcmId[]										=	$gcm_id;
					$this->Notification->save($Notif,array("validate"=>false));
				}
				
				if(!empty($arrGcmId))
				{
					$res 						=	array();
					$res['data']['title'] 		=	$title;
					$res['data']['message'] 	=	$message;
					$res['data']['class_name'] 	=	($detailTask['Task']['task_type_id'] == "1") ? 'AssignDriverNewActivity' : 'AssignTechnisian';
					$res['data']['params'] 		=	array(
														array(
															"key"	=>	"task_id",
															"val"	=>	$taskId
														),
														array(
															"key"	=>	"order_id",
															"val"	=>	$detailOrder["Order"]["id"]
														)
													);
													
					$res['data']['created'] 	=	$created;
					$res['data']['notification_group_id'] 	=	$notificationGroupId;
					$fields = array(
						"registration_ids" 		=>	$arrGcmId,
						"data" 					=>	$res,
						"priority"				=>	"high",
						"time_to_live"			=>	2419200
					);
					$push	=	$this->General->sendPushNotification($fields);
				}
			}
			
			if(in_array($updateStatus,array("5","6","7")))
			{
				$title			=	'INDORACK';
				$message		=	"";
				if($updateStatus == "5")
				{
					if($detailTask["Task"]["task_type_id"] == "1"){
						$message    	=	"Driver in on the way to deliver your item ".$detailOrder['Order']['order_no'];	
						$messageHistory =	"Driver mengantar pesanan";
						$statusHistory	=	"1";
					}
					else{
						$message    	=	"Technician in on the way to assembly your item ".$detailOrder['Order']['order_no'];	
						$messageHistory =	"Teknisi merakit pesanan";
						$statusHistory	=	"2";
					}

					$this->loadModel("OrderHistory");
					$request["OrderHistory"]["user_id"]			=	$employeeId;
					$request["OrderHistory"]["order_id"]		=	$orderId;
					$request["OrderHistory"]["description"]		= 	$messageHistory;
					$request["OrderHistory"]["status"]			=	$statusHistory;

					$this->OrderHistory->saveAll($request,array("validate"=>false));
						
				}
				else if($updateStatus == "6")
				{
					if($detailTask["Task"]["task_type_id"] == "1")
						$message    	=	"Your item is successfully delivered ".$detailOrder['Order']['order_no'];	
					else
						$message    	=	"Your item has completely assembly ".$detailOrder['Order']['order_no'];	
				}
				else if($updateStatus == "7")
				{
					if($detailTask["Task"]["task_type_id"] == "1")
						$message    	=	"Your item is failed delivered ".$detailOrder['Order']['order_no'];	
					else
						$message    	=	"Your item failed assembly ".$detailOrder['Order']['order_no'];	
					
				}
				
				$description   	=	"PO No. : ".$detailOrder['Order']['order_no']."<br/>Delivery No. : ".$detailOrder['Order']['delivery_no']."<br/>To : ".$detailOrder["Order"]["receiver_name"]."(".$detailOrder["Order"]["address"].")";
				$created		=	date("Y-m-d H:i:s");
				
				//CREATE NOTIFICATION GROUP
				$this->loadModel("NotificationGroup");
				$this->NotificationGroup->create();
				$this->NotificationGroup->saveAll(
					array(
						"created"	=>	$created
					),
					array(
						"validate"	=>	false
					)
				);
				$notificationGroupId	=	$this->NotificationGroup->id;
				
				$this->Notification->create();
				$Notif["Notification"]["user_id"]					=	$detailCustomer["User"]["id"];
				$Notif["Notification"]["gcm_id"]					=	empty($detailCustomer["User"]["gcm_id"]) ? NULL : $detailCustomer["User"]["gcm_id"];
				$Notif["Notification"]["notification_group_id"] 	=	$notificationGroupId;
				$Notif["Notification"]["order_id"]					=	$detailOrder['Order']['id'];
				$Notif["Notification"]["title"]						=	$title;
				$Notif["Notification"]["params"]					=	json_encode(
																			array(
																				array(
																					"key"	=>	"order_id",
																					"val"	=>	$detailOrder["Order"]["id"]
																				)
																			)
																		);
				$Notif["Notification"]["android_class_name"]		=	"DetailOrder";
				$Notif["Notification"]["message"]					=	$message;
				$Notif["Notification"]["description"]				=	$description;
				$Notif["Notification"]["created"]					=	$created;
				$this->Notification->save($Notif,array("validate"=>false));
				
				if(!empty($detailCustomer["User"]["gcm_id"]))
				{
					$res 						=	array();
					$res['data']['title'] 		=	$title;
					$res['data']['message'] 	=	$message;
					$res['data']['class_name'] 	=	"DetailOrder";
					$res['data']['params'] 		=	array(
														array(
															"key"	=>	"order_id",
															"val"	=>	$detailOrder["Order"]["id"]
														)
													);
													
					$res['data']['created'] 	=	$created;
					$res['data']['notification_group_id'] 	=	$notificationGroupId;
					$fields = array(
						"registration_ids" 		=>	$detailCustomer["User"]["gcm_id"],
						"data" 					=>	$res,
						"priority"				=>	"high",
						"time_to_live"			=>	2419200
					);
					$push	=	$this->General->sendPushNotification($fields);
				}
			}
			//=========== SAVE NOTIFICATION ================//
										
										
			if(count($checkTaskAssign)	==	1)
			{
				$this->Task->updateAll(
					array(
						"status"	=>	$checkTaskAssign[0]["TaskAssign"]["status"]
					),
					array(
						"Task.id"	=>	$taskId
					)
				);
				
				if($detailTask["Task"]["task_type_id"] == "2")
				{
					$updateArray	=	array(
											"assembly_status"	=>	$checkTaskAssign[0]["TaskAssign"]["status"]
										);
				}
				else if($detailTask["Task"]["task_type_id"] == "1")
				{
					$updateArray	=	array(
											"delivery_status"	=>	$checkTaskAssign[0]["TaskAssign"]["status"]
										);
				}

				$this->Order->updateAll(
					$updateArray,
					array(
						"Order.id"	=>	$orderId
					)
				);
			}
			else
			{
				$checkTaskAssign		=	$this->TaskAssign->find("first",array(
												"conditions"	=>	array(
													"TaskAssign.task_id"	=>	$taskId,
													"NOT"		=>	array(
														"TaskAssign.status"	=>	array(4,8)
													)
												),
												"group"		=>	"TaskAssign.status",
												"order"		=>	"TaskAssign.id desc"
											));
								
				$this->Task->updateAll(
					array(
						"status"	=>	$checkTaskAssign["TaskAssign"]["status"]
					),
					array(
						"Task.id"	=>	$taskId
					)
				);

				if($detailTask["Task"]["task_type_id"] == "2")
				{
					$updateArray	=	array(
											"assembly_status"	=>	$checkTaskAssign["TaskAssign"]["status"]
										);
				}
				else if($detailTask["Task"]["task_type_id"] == "1")
				{
					$updateArray	=	array(
											"delivery_status"	=>	$checkTaskAssign["TaskAssign"]["status"]
										);
				}
				
				
				$this->Order->updateAll(
					$updateArray,
					array(
						"Order.id"	=>	$orderId
					)
				);
			}
			
			if(in_array($updateStatus,array(5,6,7)))
			{
				$this->TaskAssign->updateAll(
					array(
						"status"				=> $updateStatus
					),
					array(
						"TaskAssign.task_id"	=>	$taskId,
						"NOT"	=>	array(
							"TaskAssign.status" => array(4,8)
						)
					)
				);
				
				$checkTaskAssign	=	$this->TaskAssign->find("all",array(
											"conditions"	=>	array(
												"TaskAssign.task_id"	=>	$taskId,
												"NOT"	=>	array(
													"TaskAssign.employee_id"	=>	$employeeId,
													"TaskAssign.status"			=>	4
												)
											)
										));
				
				foreach($checkTaskAssign as $checkTaskAssign)
				{
					$this->TaskHistory->create();
					$this->TaskHistory->saveAll(
						array(
							"task_id"		=>	$taskId,
							"employee_id"	=>	$checkTaskAssign["TaskAssign"]["employee_id"],
							"order_id"		=>	$orderId,
							"status"		=>	$updateStatus,
						),
						array(
							"validate"	=>	false
						)
					);
				}
				
				$this->Task->updateAll(
					array(
						"status"	=>	$updateStatus
					),
					array(
						"Task.id"	=>	$taskId
					)
				);

				$this->Order->updateAll(
					$updateArray,
					array(
						"Order.id"	=>	$orderId
					)
				);
				
				if($updateStatus == "6")
				{
					if(!empty($_FILES['images']['name']))
					{
						$tmp_name							=	$_FILES['images']["name"];
						$tmp								=	$_FILES['images']["tmp_name"];
						
		
						$path_tmp							=	ROOT.DS.'app'.DS.'tmp'.DS.'upload'.DS;
							if(!is_dir($path_tmp)) mkdir($path_tmp,0777);
		
						$ext								=	pathinfo($tmp_name,PATHINFO_EXTENSION);
						$tmp_file_name						=	md5(time());
						$tmp_images1_img					=	$path_tmp.$tmp_file_name.".".$ext;
						$upload 							=	move_uploaded_file($tmp,$tmp_images1_img);
						
						if($upload)
						{
							$mime_type						=	mime_content_type($tmp_images1_img);
							$resize							=	$this->General->ResizeImageContent(
																  $tmp_images1_img,
																  $this->settings["cms_url"],
																  $taskId,
																  "Task",
																  "maxwidth",
																  $mime_type,
																  800,
																  300,
																  "resizeMaxWidth"
															  );
		
						}
						@unlink($tmp_images1_img);
					}

					//ORDER HISTORY
					$this->loadModel("OrderHistory");
					$request["OrderHistory"]["user_id"]			=	$employeeId;
					$request["OrderHistory"]["order_id"]		=	$orderId;
					
					if($detailTask["Task"]["task_type_id"] == "2")
					{
						$request["OrderHistory"]["description"]		=	"Barang sudah dirakit";
						$request["OrderHistory"]["status"]			=	"2";
					}
					else if($detailTask["Task"]["task_type_id"] == "1")
					{
						$request["OrderHistory"]["description"]		=	"Pesanan telah sampai";
						$request["OrderHistory"]["status"]			=	"1";
					}
					
					$this->OrderHistory->saveAll($request,array("validate"=>false));
				}
			}
		}
		else
		{
			$status		=	false;
			foreach($error as $k => $v)
			{
				$message	=	$v[0];
				break;
			}
			$code		=	"03";
			$data		=	null;
		}

		$out			=	array(
			"status"				=>	$status,
			"message"				=>	$message,
			"data"					=>	$data,
			"code"					=>	$code,
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);


		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	
	function DetailTaskHistory()
	{
		$status				=	true;
		$message			=	ERR_02;
		$code				=	"02";
		$data				=	array();
		$order_id			=	NULL;
		$driver				=	array();
		
		if(isset($_REQUEST['task_history_id']))
			$task_history_id	=	$_REQUEST['task_history_id'];
		
		if(isset($_REQUEST['employee_id']))
			$employee_id	=	$_REQUEST['employee_id'];
		
		//CHECK USER ACCESS
		$this->loadModel("User");
		$checkUser	=	$this->User->find("first",array(
							"conditions"		=>	array(
								"User.id"		=>	$employee_id,
								"User.status"	=>	"1"
							)
						));
						
		if(empty($checkUser))
		{
			$status			=	false;
			$message		=	"You don't have priveledges";
		}
		else
		{
			$this->loadModel("TaskHistory");
			$joins			=	array(
									array(
										"table"			=>	"orders",
										"alias"			=>	"Order",
										'type'			 => 'LEFT',
										"conditions"	=>	array(
											"TaskHistory.order_id	=	Order.id"
										)
									),
									array(
										"table"			=>	"tasks",
										"alias"			=>	"Task",
										'type'			 => 'LEFT',
										"conditions"	=>	array(
											"Task.id	=	TaskHistory.task_id"
										)
									),
									array(
										"table"			=>	"task_statuses",
										"alias"			=>	"TaskStatus",
										'type'			 => 'LEFT',
										"conditions"	=>	array(
											"TaskHistory.status	=	TaskStatus.id"
										)
									),
									array(
										"table"			=>	"users",
										"alias"			=>	"Customer",
										'type'			 => 'LEFT',
										"conditions"	=>	array(
											"Order.customer_id	=	Customer.id"
										)
									),
									array(
										"table"			=>	"users",
										"alias"			=>	"Employee",
										'type'			 => 'LEFT',
										"conditions"	=>	array(
											"TaskHistory.employee_id	=	Employee.id"
										)
									),
									array(
										"table"			=>	"contents",
										"alias"			=>	"EmployeeImage",
										'type'			 => 'LEFT',
										"conditions"	=>	array(
											"
													EmployeeImage.model_id	=	TaskHistory.employee_id
												AND
													EmployeeImage.model		=	'User'
												AND
													EmployeeImage.type		=	'maxwidth'
											"
										)
									),
									array(
										"table"			=>	"contents",
										"alias"			=>	"TaskImage",
										'type'			 => 'LEFT',
										"conditions"	=>	array(
											"
													TaskImage.model_id	=	TaskHistory.task_id
												AND
													TaskImage.model		=	'Task'
												AND
													TaskImage.type		=	'maxwidth'
											"
										)
									)
								);
								
			$data			=	$this->TaskHistory->find("first",array(
									"conditions"	=>	array(
										"TaskHistory.id"	=>	$task_history_id
									),
									"joins"			=>	$joins,
									"fields"		=>	array(
										"TaskStatus.*",
										"Task.task_type_id",
										"TaskHistory.id",
										"TaskHistory.status",
										"TaskHistory.task_id",
										"TaskHistory.reason",
										"Order.*",
										"Customer.*",
										"Employee.*",
										"EmployeeImage.*",
										"TaskImage.*"
									)
								));
								
			if(!empty($data))
			{
				$message		=	"OK";
				
				
				//FIND PRODUCT
				$this->loadModel("OrderProduct");
				$OrderProduct		=	$this->OrderProduct->find("all",array(
											"conditions"	=>	array(
												"OrderProduct.order_id"	=>	$data["Order"]["id"]
											),
											"joins"	=>	array(
												array(
													"table"			=>	"products",
													"alias"			=>	"Product",
													'type'			=> 'LEFT',
													"conditions"	=>	array(
														"OrderProduct.product_id	=	Product.id"
													)
												),
												array(
													"table"			=>	"product_images",
													"alias"			=>	"ProductImage",
													'type'			=> 'LEFT',
													"conditions"	=>	array(
														"ProductImage.product_id	=	Product.id"
													)
												),
												array(
													"table"			=>	"contents",
													"alias"			=>	"Image",
													'type'			=> 'LEFT',
													"conditions"	=>	array(
														"
																Image.model_id	=	ProductImage.id
															AND
																Image.model		=	'ProductImage'
															AND
																Image.type		=	'maxwidth'
														"
													)
												)
											),
											"fields"	=>	array(
												"OrderProduct.qty",
												"OrderProduct.description",
												"Product.name",
												"Product.code",
												"ProductImage.id",
												"Image.host",
												"Image.url",
												"Image.modified"
											)
										));
										
				$data['Product']	=	!empty($OrderProduct) ? $OrderProduct : array();
			}
			else
			{
				$status			=	false;
				$message		=	"Information not found!";
			}
		}
		
		
		$out	=	array(
						"status"		=>	$status,
						"message"		=>	$message,
						"data"			=>	$data,
						"code"			=>	$code,
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function CustomerOrderList()
	{
		$status			=	false;
		$message		=	ERR_03;
		$data			=	null;
		$code			=	"03";
		$user_id		=	$_REQUEST['user_id'];
		$page			=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		
		//CHECK USER ID
		$this->loadModel("User");
		$check			=	$this->User->find("first",array(
								"conditions"	=>	array(
									"User.aro_id"	=>	"7",
									"User.id"		=>	$user_id
								)
							));
		
		if(empty($check))
		{				
			$out	=	array(
							"status"	=>	false,
							"message"	=>	"Not authorized",
							"data"		=>	array(),
							"code"		=>	"00",
							"pageCount"	=>	0,
							"page"		=>	0,
							"totalData"	=>	0,
							"nextPage"	=>	false,
							"request"	=>	$_REQUEST
						);
			
			$json		=	json_encode($out);
			$this->response->type('json');
			$this->response->body($json);
			return;
		}
		
		
		$this->loadModel("Order");
		$this->Order->bindModel(array(
			"hasMany"	=>	array(
				"OrderProduct"
			),
			"belongsTo"	=>	array(
				"DeliveryStatus"	=>	array(
					"className"		=>	"task_statuses",
					"foreignKey"	=>	"delivery_status"
				),
				"AssemblingStatus"	=>	array(
					"className"		=>	"task_statuses",
					"foreignKey"	=>	"assembly_status"
				),
				"PickupStatus"	=>	array(
					"className"		=>	"task_statuses",
					"foreignKey"	=>	"pickup_status"
				)
			)
		),false);
		
		$this->Order->OrderProduct->bindModel(array(
			"belongsTo"	=>	array(
				"Product"	=>	array(
					"fields"	=>	array(
						"Product.name",
						"Product.code"
					)
				)
			)
		),false);
							
		$conditions			=	array(
									"Order.customer_id"	=>	$user_id
								);
								
		$this->paginate		=	array(
			"Order"	=>	array(
				"order"			=>	"Order.id desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"conditions"	=>	$conditions,
				"recursive"		=>	3,
				"fields"		=>	array(
					"Order.*",
					"DeliveryStatus.id",
					"DeliveryStatus.name_customer",
					"AssemblingStatus.name_customer"
				)
			)
		);
		
		try
		{
			$fData			=	$this->paginate("Order");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['Order']['pageCount'],
						"page"		=>	$this->params['paging']['Order']['page'],
						"totalData"	=>	$this->params['paging']['Order']['count'],
						"nextPage"	=>	$this->params['paging']['Order']['nextPage'],
						"request"	=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function NotificationList()
	{
		$status			=	false;
		$message		=	ERR_03;
		$data			=	null;
		$code			=	"03";
		$user_id		=	$_REQUEST['user_id'];
		$page			=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		
		//CHECK USER ID
		$this->loadModel("User");
		$check			=	$this->User->find("first",array(
								"conditions"	=>	array(
									"User.id"		=>	$user_id
								)
							));
		
		if(empty($check))
		{				
			$out	=	array(
							"status"	=>	false,
							"message"	=>	"Not authorized",
							"data"		=>	array(),
							"code"		=>	"00",
							"request"	=>	$_REQUEST
						);
			
			$json		=	json_encode($out);
			$this->response->type('json');
			$this->response->body($json);
			return;
		}
		
		
		$this->loadModel("Notification");
		$conditions			=	array(
									"Notification.user_id"	=>	$user_id
								);
								
		$totalNotRead		=	$this->Notification->find("count",array(
									"conditions"	=>	array(
										"Notification.user_id"		=>	$user_id,
										"Notification.is_readed"	=>	"0"
									)
								));
								
		$this->paginate		=	array(
			"Notification"	=>	array(
				"order"			=>	"Notification.created desc,Notification.is_readed desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"conditions"	=>	$conditions,
				"recursive"		=>	3,
				"fields"		=>	array(
					"Notification.*"
				)
			)
		);
		
		try
		{
			$fData			=	$this->paginate("Notification");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"totalNotRead"	=>	$totalNotRead,
						"pageCount"	=>	$this->params['paging']['Notification']['pageCount'],
						"page"		=>	$this->params['paging']['Notification']['page'],
						"totalData"	=>	$this->params['paging']['Notification']['count'],
						"nextPage"	=>	$this->params['paging']['Notification']['nextPage'],
						"request"	=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	
	function TrackingEmployee()
	{
		$employee_id	=	isset($_REQUEST['employee_id']) ? (!empty($_REQUEST['employee_id']) ? $_REQUEST['employee_id'] : NULL) : NULL;
		
		$this->loadModel("User");
		$check		=	$this->User->find("first",array(
							"conditions"	=>	array(
								"User.id"	=>	$employee_id
							),
							"fields"		=>	array(
								"User.current_latitude",
								"User.current_longitude"
							)
						));
						
		$latitude	=	empty($check["User"]["current_latitude"]) ? NULL : $check["User"]["current_latitude"];
		
		$longitude	=	empty($check["User"]["current_longitude"]) ? NULL : $check["User"]["current_longitude"];
					
		$json		=	json_encode(array(
							"latitude"	=>	$latitude,
							"longitude"	=>	$longitude
						));
						
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function Rating()
	{
		$status			=	false;
		$message		=	ERR_04;
		$code			=	"04";
		$data			=	array();
        $checkinDetail  =   array();

		$request["Rating"]["user_id"]		=	empty($_REQUEST["user_id"]) ? "" : $_REQUEST["user_id"];
		$request["Rating"]["employee_id"]	=	empty($_REQUEST["employee_id"]) ? "" : $_REQUEST["employee_id"];
		$request["Rating"]["task_id"]		=	empty($_REQUEST["task_id"]) ? "" : $_REQUEST["task_id"];
		$request["Rating"]["star"]			=	empty($_REQUEST["star"]) ? NULL : $_REQUEST["star"];
		$request["Rating"]["description"]	=	empty($_REQUEST["description"]) ? NULL : $_REQUEST["description"];
		
		$this->loadModel('Rating');
		$this->Rating->set($request);
		$this->Rating->ValidateData();
		$error									=	$this->Rating->InvalidFields();
		if(empty($error))
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			
			$this->Rating->save($request,array("validate"=>false));
			$ID			=	$this->Rating->id;
			$data		=	$this->Rating->find('first',array(
								"conditions"	=>	array(
									"Rating.id"		=>	$ID
								)
							));
		}
		else
		{
			$status		=	false;
			foreach($error as $k => $v)
			{
				$message	=	$v[0];
				break;
			}
			$code		=	"03";
			$data		=	null;
		}

		$out			=	array(
			"status"				=>	$status,
			"message"				=>	$message,
			"data"					=>	$data,
			"code"					=>	$code,
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);


		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	
	function SuperadminDriverJobList()
	{
		$status			=	false;
		$message		=	ERR_03;
		$data			=	null;
		$code			=	"03";
		$user_id		=	$_REQUEST['user_id'];
		$task_type_id	=	$_REQUEST['task_type_id'];
		$page			=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		
		
		$this->loadModel("TaskHistory");
		$joins			=	array(
								array(
									"table"			=>	"tasks",
									"alias"			=>	"Task",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"Task.id		=	TaskHistory.task_id"
									)
								),
								array(
									"table"			=>	"orders",
									"alias"			=>	"Order",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"TaskHistory.order_id		=	Order.id"
									)
								),
								array(
									"table"			=>	"task_statuses",
									"alias"			=>	"TaskStatus",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"TaskHistory.status	=	TaskStatus.id"
									)
								),
								array(
									"table"			=>	"users",
									"alias"			=>	"Employee",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"TaskHistory.employee_id	=	Employee.id"
									)
								),
								array(
									"table"			=>	"users",
									"alias"			=>	"Customer",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"Order.customer_id	=	Customer.id"
									)
								),
								array(
									"table"			=>	"contents",
									"alias"			=>	"Thumbnail",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"
												Thumbnail.model_id	=	Employee.id
											AND
												Thumbnail.model		=	'User'
											AND
												Thumbnail.type		=	'square'
										"
									)
								)
							);
		
		
		$conditions			=	array(
									"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id AND employee_id = TaskHistory.employee_id) AND Task.task_type_id = '".$task_type_id."'"
								);
								
		$this->paginate		=	array(
			"TaskHistory"	=>	array(
				"order"			=>	"TaskHistory.id desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"joins"			=>	$joins,
				"conditions"	=>	$conditions,
				"fields"		=>	array(
					"TaskHistory.id",
					"Order.*",
					"Task.vehicle_no",
					"TaskStatus.name",
					"TaskStatus.color",
					"Employee.id",
					"Employee.firstname",
					"Employee.lastname",
					"Customer.firstname",
					"Customer.lastname",
					"Thumbnail.id",
					"Thumbnail.modified",
					"Thumbnail.host",
					"Thumbnail.url"
				),
				"group"			=>	array(
					"TaskHistory.task_id",
					"TaskHistory.employee_id"
				)
			)
		);
		
		try
		{
			$fData			=	$this->paginate("TaskHistory");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['TaskHistory']['pageCount'],
						"page"		=>	$this->params['paging']['TaskHistory']['page'],
						"totalData"	=>	$this->params['paging']['TaskHistory']['count'],
						"nextPage"	=>	$this->params['paging']['TaskHistory']['nextPage'],
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function ProductList()
	{
		$status			=	true;
		$message		=	ERR_03;
		$data			=	array();
		$code			=	"03";
		
		$user_id		=	isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : NULL;
		$category_id	=	isset($_REQUEST['category_id']) ? $_REQUEST['category_id'] : NULL;
		$page			=	(!isset($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		
		
		
		$joins			=	array(
								array(
									"table"			=>	"product_images",
									"alias"			=>	"ProductImage",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"ProductImage.id = (SELECT MIN(id) FROM product_images WHERE product_id = Product.id)"
									)
								),
								array(
									"table"			=>	"contents",
									"alias"			=>	"Content",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"
												Content.model_id = ProductImage.id
											AND
												Content.model	=	'ProductImage'
											AND
												Content.type	=	'maxwidth'
										"
									)
								)
							);		
							
							
		$conditions		=	array(
								"Product.status" 		=>	"1",
								"Product.category_id" 	=>	$category_id
							);
							
		$fields			=	array(
								"Product.*",
								"Content.*"
							);
							
		$group			=	"Product.id";
		$order			=	"Product.id desc";
		
		$this->loadModel("Product");
		$this->paginate		=	array(
			"Product"	=>	array(
				"page"			=>	$page,		
				"limit"			=>	10,
				"joins"			=>	$joins,
				"conditions"	=>	$conditions,
				"order"			=>	$order,
				"recursive"		=>	3,
				"fields"		=>	$fields
			)
		);
		
		try
		{
			$fData		=	$this->paginate("Product");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	false;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['Product']['pageCount'],
						"page"		=>	$this->params['paging']['Product']['page'],
						"totalData"	=>	$this->params['paging']['Product']['count'],
						"nextPage"	=>	$this->params['paging']['Product']['nextPage'],
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function CategoryList()
	{
		$status			=	true;
		$message		=	ERR_03;
		$data			=	array();
		$code			=	"03";
		
		$user_id		=	isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : NULL;
		$category_id	=	isset($_REQUEST['category_id']) ? $_REQUEST['category_id'] : NULL;
		$page			=	(!isset($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
							
							
		$conditions		=	array(
								"ProductCategory.status" =>	"1"
							);
							
		$order			=	"ProductCategory.id desc";
		
		$this->loadModel("ProductCategory");
		$this->ProductCategory->BindImageContent(false);
		
		$this->paginate		=	array(
			"ProductCategory"	=>	array(
				"page"			=>	$page,		
				"limit"			=>	10,
				
				"conditions"	=>	$conditions,
				"order"			=>	$order,
				"recursive"		=>	3
			)
		);
		
		try
		{
			$fData		=	$this->paginate("ProductCategory");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	false;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['ProductCategory']['pageCount'],
						"page"		=>	$this->params['paging']['ProductCategory']['page'],
						"totalData"	=>	$this->params['paging']['ProductCategory']['count'],
						"nextPage"	=>	$this->params['paging']['ProductCategory']['nextPage'],
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function ProductDetail()
	{
		$status			=	true;
		$message		=	ERR_03;
		$data			=	array();
		$code			=	"03";
		
		$user_id		=	isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : NULL;
		$productId		=	isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : NULL;
		
						
							
		$conditions		=	array(
								"Product.status" 	=>	"1",
								"Product.id" 		=>	$productId
							);
							
		$fields			=	array(
								"Product.*",
								"Content.*"
							);
							
		$group			=	"Product.id";
		$order			=	"Product.id desc";
		
		$this->loadModel("Product");
		$this->Product->bindModel(array(
			"hasMany"	=>	array(
				"ProductImage"	=>	array(
					"order"	=>	"ProductImage.id asc"
				)
			)
		));
		$this->Product->ProductImage->bindModel(array(
			"hasOne"	=>	array(
				"Image"	=>	array(
					"className"		=>	"Content",
					"foreignKey"	=>	"model_id",
					"conditions"	=>	array(
						"
								Image.model	=	'ProductImage'
							AND
								Image.type	=	'maxwidth'
						"
					)
				)
			)
		));
		
		$fData		=	$this->Product->find("first",array(
							"conditions"	=>	$conditions,
							"order"			=>	$order,
							"recursive"		=>	3
						));
		
		
		if(empty($fData))
		{
			$status		=	false;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}
	
	function DownloadPdf()
	{
		$ID				=	isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : NULL;

		//CHECK FILES
		$pathPdf		=	$this->settings["path_content"]."Product/".$ID."/".$ID."_pdf.pdf";
		
		if(file_exists($pathPdf))
		{
			//CHECK META DATA
			$this->loadModel("Product");
			$detail		=	$this->Product->find("first",array(
								"conditions"	=>	array(
									"Product.id"		=>	$ID,
									"Product.status"	=>	"1"	
								)
							));
							
			if(!empty($detail))
			{
				$fileNameDownload	=	Inflector::slug(strtolower($detail["Product"]["name"]),"-").".pdf";
				$this->response->file(
					$pathPdf,
					array(
						'download' 	=> true, 
						'name' 		=> $fileNameDownload
					)
				);
				return $this->response;
			}
		}
	}


	function SalesOrderList()
	{
		$status			=	false;
		$message		=	ERR_03;
		$data			=	null;
		$code			=	"03";
		$user_id		=	$_REQUEST['user_id'];
		$page			=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];

		$this->loadModel("SalesOrder");					
		$conditions			=	array(
									"SalesOrder.user_id"	=>	$user_id
								);
		$this->loadModel("Order");
		$this->loadModel("Task");
		$this->SalesOrder->bindModel(array(
				"hasOne"	=>	array(
					"Order"	=>	array(
						"className"		=>	"Order",
						"foreignKey"	=>	false,
						"conditions"	=>	array(
							"SalesOrder.order_id = Order.id"
						),
						"fields"		=>	array(
							"Order.id",
							"Order.delivery_status",
							"Order.assembly_status",
							"Order.order_no",
							"Order.delivery_type_id",
							"Order.pickup_status",
							"Order.delivery_type_id"
						)
					),
					"Task"	=> array(
						"className"		=>	"Task",
						"foreignKey"	=>	false,
						"conditions"	=>	array(
							"SalesOrder.order_id = Task.order_id"
						),
						"fields"		=>	array(
							"id",
							"task_type_id",
							"status"
						)
					)
				)
			)
		);
		
		$group 			=	"SalesOrder.id";
		$order			=	"SalesOrder.modified desc";

		$this->paginate		=	array(
			"SalesOrder"	=>	array(
				"order"			=>	"SalesOrder.id desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"conditions"	=>	$conditions,
				"group"			=>	$group,
				"order"			=>	$order,
				"recursive"		=>	3
			)
		);
		
		try
		{
			$fData			=	$this->paginate("SalesOrder");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['SalesOrder']['pageCount'],
						"page"		=>	$this->params['paging']['SalesOrder']['page'],
						"totalData"	=>	$this->params['paging']['SalesOrder']['count'],
						"nextPage"	=>	$this->params['paging']['SalesOrder']['nextPage'],
						"request"	=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	public function addSalesOrder()
	{
		$status			=	false;
		$message		=	ERR_04;
		$code			=	"04";
		$data			=	array();
        $checkinDetail  =   array();

		$request["SalesOrder"]["custname"]		=	empty($_REQUEST["custname"]) ? "" : $_REQUEST["custname"];
		$request["SalesOrder"]["notlp"]			=	empty($_REQUEST["notlp"]) ? "" : $_REQUEST["notlp"];
		$request["SalesOrder"]["alamat"]		=	empty($_REQUEST["alamat"]) ? "" : $_REQUEST["alamat"];
		$request["SalesOrder"]["description"]	=	empty($_REQUEST["description"]) ? NULL : $_REQUEST["description"];
		$request["SalesOrder"]["user_id"]		=	empty($_REQUEST["user_id"]) ? NULL : $_REQUEST["user_id"];

		$user_id 		=	empty($_REQUEST["user_id"]) ? NULL : $_REQUEST["user_id"];
		//$request["SalesOrder"]["status"]		=	empty($_REQUEST["status"]) ? NULL : $_REQUEST["status"];
		
		//CHECK USER ACCESS
		$this->loadModel("User");
		$checkUser	=	$this->User->find("first",array(
							"conditions"		=>	array(
								"User.id"		=>	$user_id,
								"User.status"	=>	"1"
							)
						));
						
		if(empty($checkUser))
		{
			$status			=	false;
			$message		=	"You don't have priveledges";
		}
		else
		{
			$this->loadModel('SalesOrder');
			$this->SalesOrder->set($request);
			//$this->SalesOrder->ValidateData();
			//$error									=	$this->SalesOrder->InvalidFields();
			if(empty($error))
			{
				$status		=	true;
				$message	=	ERR_00;
				$code		=	"00";
				
				$this->SalesOrder->save($request,array("validate"=>false));
				$salesPO_id			=	$this->SalesOrder->getLastInsertId();

				if (!empty($salesPO_id)) {
					$this->loadModel("OrderHistory");
					$datasave["OrderHistory"]["user_id"]			=	$user_id;
					$datasave["OrderHistory"]["order_id"]			=	"0";
					$datasave["OrderHistory"]["sales_po_id"]		=	$salesPO_id;
					$datasave["OrderHistory"]["description"]		=	"PO Telah dibuat";
					$datasave["OrderHistory"]["taskType"]			=	"1";
					$datasave["OrderHistory"]["status"]				=	"0";
					$this->OrderHistory->set($datasave);
					$this->OrderHistory->saveAll($datasave,array("validate"=>false));

				}
			}
			else
			{
				$status		=	false;
				foreach($error as $k => $v)
				{
					$message	=	$v[0];
					break;
				}
				$code		=	"03";
				$data		=	null;
			}
		}

		$out			=	array(
			"status"				=>	$status,
			"message"				=>	$message,
			"data"					=>	$data,
			"code"					=>	$code,
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);


		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	public function updateSalesOrder()
	{
		$status			=	false;
		$message		=	ERR_04;
		$code			=	"04";
		$data			=	array();
        $checkinDetail  =   array();

		$request["SalesOrder"]["custname"]		=	empty($_REQUEST["custname"]) ? "" : $_REQUEST["custname"];
		$request["SalesOrder"]["notlp"]			=	empty($_REQUEST["notlp"]) ? "" : $_REQUEST["notlp"];
		$request["SalesOrder"]["alamat"]		=	empty($_REQUEST["alamat"]) ? "" : $_REQUEST["alamat"];
		$request["SalesOrder"]["description"]	=	empty($_REQUEST["description"]) ? NULL : $_REQUEST["description"];
		$request["SalesOrder"]["user_id"]		=	empty($_REQUEST["user_id"]) ? NULL : $_REQUEST["user_id"];

		$user_id 		=	empty($_REQUEST["user_id"]) ? NULL : $_REQUEST["user_id"];
		$salesOrderId	=	empty($_REQUEST["id"]) ? NULL : $_REQUEST["id"];
		//$request["SalesOrder"]["status"]		=	empty($_REQUEST["status"]) ? NULL : $_REQUEST["status"];
		
		//CHECK USER ACCESS
		$this->loadModel("User");	
		$checkUser	=	$this->User->find("first",array(
							"conditions"		=>	array(
								"User.id"		=>	$user_id,
								"User.status"	=>	"1"
							)
						));
						
		if(empty($checkUser))
		{
			$status			=	false;
			$message		=	"You don't have priveledges";
		}
		else
		{
			$this->loadModel('SalesOrder');
			$this->SalesOrder->set($request);
			//$this->SalesOrder->ValidateData();
			//$error									=	$this->SalesOrder->InvalidFields();
			if(empty($error))
			{
				$status		=	true;
				$message	=	ERR_00;
				$code		=	"00";
				
				//$this->SalesOrder->save($request,array("validate"=>false));
				$this->SalesOrder->updateAll(
					array(
						"SalesOrder.custname" 		=>	"'".$request["SalesOrder"]["custname"]."'",
						"SalesOrder.notlp"			=>	"'".$request["SalesOrder"]["notlp"]."'",
						"SalesOrder.alamat"			=>	"'".$request["SalesOrder"]["alamat"]."'",
						"SalesOrder.description"	=>	"'".$request["SalesOrder"]["description"]."'"
					),
						array(
							"SalesOrder.id"				=>	$salesOrderId
						), true
					);
			}
			else
			{
				$status		=	false;
				foreach($error as $k => $v)
				{
					$message	=	$v[0];
					break;
				}
				$code		=	"03";
				$data		=	null;
			}
		}

		$out			=	array(
			"status"				=>	$status,
			"message"				=>	$message,
			"data"					=>	$data,
			"code"					=>	$code,
			"request"				=>	$_REQUEST,
			"file"					=>	$_FILES
		);


		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	function DetailSalesOrder()
	{
		$status				=	true;
		$message			=	ERR_02;
		$code				=	"02";
		$data				=	array();
		$order_id			=	NULL;
		$driver				=	array();
		
		if(isset($_REQUEST['salesOrderId']))
			$salesOrderId	=	$_REQUEST['salesOrderId'];
		
		if(isset($_REQUEST['user_id']))
			$user_id	=	$_REQUEST['user_id'];

		if(isset($_REQUEST['user_id']))
			$taskTypeId	=	$_REQUEST['taskTypeId'];
		
		//CHECK USER ACCESS
		$this->loadModel("User");
		$checkUser	=	$this->User->find("first",array(
							"conditions"		=>	array(
								"User.id"		=>	$user_id,
								"User.status"	=>	"1"
							)
						));
						
		if(empty($checkUser))
		{
			$status			=	false;
			$message		=	"You don't have priveledges";
		}
		else
		{
			$this->loadModel("SalesOrder");
			$this->loadModel("Order");
			$this->loadModel("Task");
			$this->SalesOrder->bindModel(array(
					"hasOne"	=>	array(
						"Order"	=>	array(
							"className"		=>	"Order",
							"foreignKey"	=>	false,
							"conditions"	=>	array(
								"SalesOrder.order_id = Order.id"
							),
							"fields"		=>	array(
								"Order.id",
								"Order.order_no"
							)
						),
						"Task"	=> array(
							"className"		=>	"Task",
							"foreignKey"	=>	false,
							"conditions"	=>	array(
								"SalesOrder.order_id = Task.order_id"
							),
							"fields"		=>	array(
								"id",
								"task_type_id",
								"status"
							)
						)
					)
				)
			);
			if ($taskTypeId == "1" || $taskTypeId == "2") {
				$data			=	$this->SalesOrder->find("first",array(
									"conditions"	=>	array(
										"SalesOrder.id"			=>	$salesOrderId,
										"Task.task_type_id"		=>	$taskTypeId
										//"SalesOrder.user_id"	=>	$user_id
									),
									"recursive"		=>	3
								));
			} else {
				$data			=	$this->SalesOrder->find("first",array(
									"conditions"	=>	array(
										"SalesOrder.id"			=>	$salesOrderId
										//"SalesOrder.user_id"	=>	$user_id
									),
									"recursive"		=>	3
								));
			}
			$message		=	"Success";
		}
		
		
		$out	=	array(
						"status"		=>	$status,
						"message"		=>	$message,
						"data"			=>	$data,
						"code"			=>	$code,
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	function DeleteAllSalesOrder()
	{
		$status				=	true;
		$message			=	ERR_02;
		$code				=	"02";
		$data				=	array();
		$order_id			=	NULL;
		$driver				=	array();
		
		if(isset($_REQUEST['salesOrderId']))
			$salesOrderId	=	$_REQUEST['salesOrderId'];
		
		if(isset($_REQUEST['user_id']))
			$user_id	=	$_REQUEST['user_id'];
		
		//CHECK USER ACCESS
		$this->loadModel("User");
		$checkUser	=	$this->User->find("first",array(
							"conditions"		=>	array(
								"User.id"		=>	$user_id,
								"User.status"	=>	"1"
							)
						));
						
		if(empty($checkUser))
		{
			$status			=	false;
			$message		=	"You don't have priveledges";
		}
		else
		{
			$this->loadModel("SalesOrder");
			$data			=	$this->SalesOrder->deleteAll(
									array(
										"SalesOrder.id"	=>	$salesOrderId),
									true, true);
			$message		=	"Success";
		}
		
		
		$out	=	array(
						"status"		=>	$status,
						"message"		=>	$message,
						"data"			=>	$data,
						"code"			=>	$code,
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	function HistoryList()
	{
		$status			=	false;
		$message		=	
		$co;
		$data			=	null;
		$code			=	"03";
		$user_id		=	$_REQUEST['user_id'];
		$order_id		=	$_REQUEST['order_id'];
		$task_type_id	=	$_REQUEST['task_type_id'];

		//CHECK USER ID
		$this->loadModel("User");
		$check			=	$this->User->find("first",array(
								"conditions"	=>	array(
									"User.is_admin"	=>	"1",
									"User.id"		=>	$user_id
								)
							));
		
		if(empty($check))
		{				
			$out	=	array(
							"status"	=>	false,
							"message"	=>	"Not authorized",
							"data"		=>	array(),
							"code"		=>	"00",
							"request"	=>	$_REQUEST
						);
			
			$json		=	json_encode($out);
			$this->response->type('json');
			$this->response->body($json);
			return;
		}
		
		
		$this->loadModel("OrderHistory");

		$data = $this->OrderHistory->find("all", array(
						"conditions"	=>	array(
							"OrderHistory.order_id"		=>	$order_id,
							"OrderHistory.status"	=>	$task_type_id
						)
					)
				);

		if ($data) {
			$status 	= 	true;
			$message	=	ERR_00;
			$code 		=	"00";
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	public function SearchSalesPO()
	{
		$status			=	false;
		$message		=	ERR_03;
		$data			=	null;
		$code			=	"03";
		$user_id		=	$_REQUEST['user_id'];
		$page			=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		$dateFrom		=	$_REQUEST['dateFrom']." "."00:00:01";
		$dateTo			=	$_REQUEST['dateTo']." "."23:59:59";
		$taskStatus 	=	$_REQUEST['taskStatus'];
		$custName 		=	$_REQUEST['custName'];

		$this->loadModel("SalesOrder");					
		if (!empty($custName) && $taskStatus != "all" && $taskStatus != "0") {
			$con 	=	"1 woy";
			$conditions			=	array(
									"SalesOrder.user_id"			=>	$user_id,
									"SalesOrder.custname LIKE"		=>	"%".$custName."%",
									"SalesOrder.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
									"Task.status"					=>	$taskStatus
								);
		} elseif ($taskStatus == "all" && !empty($custName)) {
			$con 	=	"2 woy";
			$conditions			=	array(
									"SalesOrder.user_id"			=>	$user_id,
									"SalesOrder.custname LIKE"		=>	"%".$custName."%",
									"SalesOrder.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
								);
		} elseif ($taskStatus == "all" && empty($custName)) {
			$con 	=	"3 woy";
			$conditions			=	array(
									"SalesOrder.user_id"			=>	$user_id,
									"SalesOrder.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
								);
		} elseif (!empty($custName) && $taskStatus == "0") {
			$con 	=	"4 woy";
			$conditions			=	array(
									"SalesOrder.user_id"			=>	$user_id,
									"SalesOrder.custname LIKE"		=>	"%".$custName."%",
									"SalesOrder.status"				=>	"0",
									"SalesOrder.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
								);
		} elseif (empty($custName) && $taskStatus == "0") {
			$con 	=	"5 woy";
			$conditions			=	array(
									"SalesOrder.user_id"			=>	$user_id,
									"SalesOrder.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
									"SalesOrder.status"				=>	"0"
								);
		} else {
			$con 	=	"6 woy";
			$conditions			=	array(
									"SalesOrder.user_id"			=>	$user_id,
									"SalesOrder.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
									"Task.status"					=>	$taskStatus
								);
		}

		$this->loadModel("Order");
		$this->loadModel("Task");
		$this->SalesOrder->bindModel(array(
				"hasOne"	=>	array(
					"Order"	=>	array(
						"className"		=>	"Order",
						"foreignKey"	=>	false,
						"conditions"	=>	array(
							"SalesOrder.order_id = Order.id"
						),
						"fields"		=>	array(
							"Order.id",
							"Order.delivery_status",
							"Order.order_no",
							"Order.delivery_type_id",
							"Order.pickup_status",
							"Order.delivery_type_id"
						)
					),
					"Task"	=> array(
						"className"		=>	"Task",
						"foreignKey"	=>	false,
						"conditions"	=>	array(
							"SalesOrder.order_id = Task.order_id"
						),
						"fields"		=>	array(
							"id",
							"task_type_id",
							"status"
						)
					)
				)
			)
		);
		
		$order			=	"SalesOrder.id desc";

		$this->paginate		=	array(
			"SalesOrder"	=>	array(
				"order"			=>	"SalesOrder.id desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"conditions"	=>	$conditions,
				"order"			=>	$order,
				"recursive"		=>	3
			)
		);
		
		try
		{
			$fData			=	$this->paginate("SalesOrder");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['SalesOrder']['pageCount'],
						"page"		=>	$this->params['paging']['SalesOrder']['page'],
						"totalData"	=>	$this->params['paging']['SalesOrder']['count'],
						"nextPage"	=>	$this->params['paging']['SalesOrder']['nextPage'],
						"request"	=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	function ConfirmDriver()
	{
		$status				=	true;
		$message			=	ERR_02;
		$code				=	"02";
		$data				=	array();
		$order_id			=	NULL;
		$driver				=	array();
		
		if(isset($_REQUEST['order_id']))
			$order_id	=	$_REQUEST['order_id'];
		
		if(isset($_REQUEST['user_id']))
			$user_id	=	$_REQUEST['user_id'];
		
		//CHECK USER ACCESS
		$this->loadModel("User");
		$checkUser	=	$this->User->find("first",array(
							"conditions"		=>	array(
								"User.id"		=>	$user_id,
								"User.status"	=>	"1"
							)
						));
						
		if(empty($checkUser))
		{
			$status			=	false;
			$message		=	"You don't have priveledges";
		}
		else
		{
			$this->loadModel("Order");
			$this->loadModel("Task");
			$this->loadModel("TaskAssign");
			$this->loadModel("TaskHistory");
			$this->loadModel("OrderHistory");

			if (!empty($order_id)) {
				//========== Update Delivery Order ==========//
				$this->Order->updateAll(
					array(
						"Order.delivery_status" 		=>	"11"
					),
						array(
							"Order.id"					=>	$order_id
						), true
				);
				//========== Update Delivery Order ==========//

				//========== Update Task ==========//
				$this->Task->updateAll(
					array(
						"Task.status" 		=>	"11"
					),
						array(
							"Task.order_id"					=>	$order_id
						), true
				);
				//========== Update Task ==========//

				//========= Get Task ID	=========//
				$taskId 	=	$this->Task->find("first", array(
										"conditions"	=>	array(
											"Task.order_id"		=>	$order_id
										)
									)
								);
				//========= Get Task ID	=========//

				//========== Update Task Assigns ==========//
				$taskAssignId 	=	$this->TaskAssign->find("first", array(
										"conditions"	=>	array(
											"TaskAssign.order_id"		=>	$order_id
										)
									)
								);
				$this->TaskAssign->updateAll(
					array(
						"TaskAssign.status" 		=>	"11"
					),
						array(
							"TaskAssign.order_id"			=>	$order_id,
							"TaskAssign.task_id"			=>	$taskId["Task"]["id"]
						), true
				);
				//========== Update Task Assigns ==========//

				//========== Add Task History ==========//
				$request["TaskHistory"]["task_id"]			=	$taskId["Task"]["id"];
				$request["TaskHistory"]["order_id"]			=	$order_id;
				$request["TaskHistory"]["employee_id"]		=	$taskAssignId["TaskAssign"]["employee_id"];
				$request["TaskHistory"]["reason"]			=	null;
				$request["TaskHistory"]["status"]			=	"11";

				$this->TaskHistory->set($request);
				$this->TaskHistory->saveAll($request,array("validate"=>false));
				//========== Add Task History ==========//		

				//========= Add Detail History =========//
				$reqOrderHistory["OrderHistory"]["user_id"]			=	$user_id;
				$reqOrderHistory["OrderHistory"]["order_id"]		=	$order_id;
				$reqOrderHistory["OrderHistory"]["description"]		=	"Driver kembali ke Gudang";
				$reqOrderHistory["OrderHistory"]["taskType"]		=	"0";
				$reqOrderHistory["OrderHistory"]["status"]			=	"1";

				$this->OrderHistory->set($reqOrderHistory);
				$this->OrderHistory->saveAll($reqOrderHistory,array("validate"=>false));
				//========= Add Detail History =========//

				$message		=	"Success";	
			}
			
		}
		
		
		$out	=	array(
						"status"		=>	$status,
						"message"		=>	$message,
						"data"			=>	$data,
						"code"			=>	$code,
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	function ConfirmTechnician()
	{
		$status				=	true;
		$message			=	ERR_02;
		$code				=	"02";
		$data				=	array();
		$order_id			=	NULL;
		$driver				=	array();
		
		if(isset($_REQUEST['order_id']))
			$order_id	=	$_REQUEST['order_id'];
		
		if(isset($_REQUEST['user_id']))
			$user_id	=	$_REQUEST['user_id'];
		
		//CHECK USER ACCESS
		$this->loadModel("User");
		$checkUser	=	$this->User->find("first",array(
							"conditions"		=>	array(
								"User.id"		=>	$user_id,
								"User.status"	=>	"1"
							)
						));
						
		if(empty($checkUser))
		{
			$status			=	false;
			$message		=	"You don't have priveledges";
		}
		else
		{
			$this->loadModel("Order");
			$this->loadModel("Task");
			$this->loadModel("TaskAssign");
			$this->loadModel("TaskHistory");
			$this->loadModel("OrderHistory");

			if (!empty($order_id)) {
				//========== Update Delivery Order ==========//
				$this->Order->updateAll(
					array(
						"Order.delivery_status" 		=>	"11"
					),
						array(
							"Order.id"					=>	$order_id
						), true
				);
				//========== Update Delivery Order ==========//

				//========== Update Task ==========//
				$this->Task->updateAll(
					array(
						"Task.status" 		=>	"11"
					),
						array(
							"Task.order_id"					=>	$order_id
						), true
				);
				//========== Update Task ==========//

				//========= Get Task ID	=========//
				$taskId 	=	$this->Task->find("first", array(
										"conditions"	=>	array(
											"Task.order_id"		=>	$order_id
										)
									)
								);
				//========= Get Task ID	=========//

				//========== Update Task Assigns ==========//
				$taskAssignId 	=	$this->TaskAssign->find("first", array(
										"conditions"	=>	array(
											"TaskAssign.order_id"		=>	$order_id
										)
									)
								);
				$this->TaskAssign->updateAll(
					array(
						"TaskAssign.status" 		=>	"11"
					),
						array(
							"TaskAssign.order_id"			=>	$order_id,
							"TaskAssign.task_id"			=>	$taskId["Task"]["id"]
						), true
				);
				//========== Update Task Assigns ==========//

				//========== Add Task History ==========//
				$request["TaskHistory"]["task_id"]			=	$taskId["Task"]["id"];
				$request["TaskHistory"]["order_id"]			=	$order_id;
				$request["TaskHistory"]["employee_id"]		=	$taskAssignId["TaskAssign"]["employee_id"];
				$request["TaskHistory"]["reason"]			=	null;
				$request["TaskHistory"]["status"]			=	"11";

				$this->TaskHistory->set($request);
				$this->TaskHistory->saveAll($request,array("validate"=>false));
				//========== Add Task History ==========//		

				//========= Add Detail History =========//
				$reqOrderHistory["OrderHistory"]["user_id"]			=	$user_id;
				$reqOrderHistory["OrderHistory"]["order_id"]		=	$order_id;
				$reqOrderHistory["OrderHistory"]["description"]		=	"Teknisi kembali ke Gudang";
				$reqOrderHistory["OrderHistory"]["taskType"]		=	"0";
				$reqOrderHistory["OrderHistory"]["status"]			=	"2";

				$this->OrderHistory->set($reqOrderHistory);
				$this->OrderHistory->saveAll($reqOrderHistory,array("validate"=>false));
				//========= Add Detail History =========//

				$message		=	"Success";	
			}
			
		}
		
		
		$out	=	array(
						"status"		=>	$status,
						"message"		=>	$message,
						"data"			=>	$data,
						"code"			=>	$code,
						"request"		=>	$_REQUEST
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	// Search Driver //
	function SearchDeliveryList()
	{
		$status					=	false;
		$message				=	ERR_03;
		$data					=	null;
		$code					=	"03";
		$user_id				=	$_REQUEST['user_id'];
		$page					=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		$task_type_id			=	(empty($_REQUEST['task_type_id'])) ? 1 : $_REQUEST['task_type_id'];
		$dateFrom				=	$_REQUEST['dateFrom']." "."00:00:01";
		$dateTo					=	$_REQUEST['dateTo']." "."23:59:59";
		$taskStatus 			=	$_REQUEST['taskStatus'];
		$custName 				=	$_REQUEST['custName'];
		
		//CHECK USER ID
		$this->loadModel("User");
		$check			=	$this->User->find("first",array(
								"conditions"	=>	array(
									"User.is_admin"	=>	"1",
									"User.id"		=>	$user_id
								)
							));
		
		if(empty($check))
		{				
			$out	=	array(
							"status"	=>	false,
							"message"	=>	"Not authorized",
							"data"		=>	array(),
							"code"		=>	"00",
							"request"	=>	$_REQUEST
						);
			
			$json		=	json_encode($out);
			$this->response->type('json');
			$this->response->body($json);
			return;
		}
		
		
		$this->loadModel("Task");
		$joins			=	array(
								array(
									"table"			=>	"orders",
									"alias"			=>	"Order",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"Task.order_id	=	Order.id"
									)
								),
								array(
									"table"			=>	"task_statuses",
									"alias"			=>	"TaskStatus",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"Task.status	=	TaskStatus.id"
									)
								),
								array(
									"table"			=>	"users",
									"alias"			=>	"Customer",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"Order.customer_id	=	Customer.id"
									)
								)
						);

		if (!empty($custName) && $taskStatus != "all" && $taskStatus != "1") {
			$con = "1 woy";
			$conditions			=	array(
									"Task.task_type_id"			=>	$task_type_id,
									"Order.receiver_name LIKE"		=>	"%".$custName."%",
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
									"Order.delivery_status"		=>	$taskStatus
								);
		} elseif ($taskStatus == "all" && !empty($custName)) {
			$con = "2 woy";
			$conditions			=	array(
									"Task.task_type_id"			=>	$task_type_id,
									"Order.receiver_name LIKE"		=>	"%".$custName."%",
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
								);
		} elseif ($taskStatus == "all" && empty($custName)) {
			$con = "3 woy";
			$conditions			=	array(
									"Task.task_type_id"			=>	$task_type_id,
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
								);
		} elseif (!empty($custName) && $taskStatus == "1") {
			$con = "4 woy";
			$conditions			=	array(
									"Task.task_type_id"			=>	$task_type_id,
									"Order.receiver_name LIKE"		=>	"%".$custName."%",
									"Order.delivery_status"			=>	$taskStatus,
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
								);
		} elseif (empty($custName) && $taskStatus == "1") {
			$con = "5 woy";
			$conditions			=	array(
									"Task.task_type_id"			=>	$task_type_id,
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
									"Order.delivery_status"				=>	$taskStatus
								);
		} else {
			$con = "6 woy";
			$conditions			=	array(
									"Task.task_type_id"			=>	$task_type_id,
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
									"Order.delivery_status"					=>	$taskStatus
								);
		}
								
		$this->paginate		=	array(
			"Task"	=>	array(
				"order"			=>	"Task.id desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"joins"			=>	$joins,
				"conditions"	=>	$conditions,
				"fields"		=>	array(
					"Task.id",
					"Task.order_id",
					"Order.order_no",
					"Order.delivery_no",
					"Order.address",
					"Order.delivery_date",
					"Order.is_urgent",
					"TaskStatus.id",
					"TaskStatus.color",
					"TaskStatus.name",
					"Order.delivery_type_id",
					"Customer.firstname",
					"Customer.lastname"
				)
			)
		);
		
		try
		{
			$fData			=	$this->paginate("Task");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['Task']['pageCount'],
						"page"		=>	$this->params['paging']['Task']['page'],
						"totalData"	=>	$this->params['paging']['Task']['count'],
						"nextPage"	=>	$this->params['paging']['Task']['nextPage'],
						"request"	=>	$_REQUEST,
						"con"		=>	$con
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	function SearchPickUp()
	{
		$status					=	false;
		$message				=	ERR_03;
		$data					=	null;
		$code					=	"03";
		$user_id				=	$_REQUEST['user_id'];
		$page					=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		$dateFrom				=	$_REQUEST['dateFrom']." "."00:00:01";
		$dateTo					=	$_REQUEST['dateTo']." "."23:59:59";
		$taskStatus 			=	$_REQUEST['taskStatus'];
		$custName 				=	$_REQUEST['custName'];
		
		//CHECK USER ID
		$this->loadModel("User");
		$check					=	$this->User->find("first",array(
										"conditions"	=>	array(
											"User.is_admin"	=>	"1",
											"User.id"		=>	$user_id
										)
									));
		
		if(empty($check))
		{				
			$out	=	array(
							"status"	=>	false,
							"message"	=>	"Not authorized",
							"data"		=>	array(),
							"code"		=>	"00",
							"request"	=>	$_REQUEST
						);
			
			$json		=	json_encode($out);
			$this->response->type('json');
			$this->response->body($json);
			return;
		}
		
		
		$this->loadModel("Order");
		$this->Order->bindModel(
		array(
			"hasMany"	=>	array(
				"OrderProduct"
			),
			"belongsTo"	=>	array(
				"Customer"	=>	array(
					"className"	=>	"User"
				),
				"PickupStatus"	=>	array(
					"className"		=>	"TaskStatus",
					"foreignKey"	=>	"pickup_status"
				)
			)
		),false);
		$this->Order->OrderProduct->bindModel(array(
			"belongsTo"	=>	array(
				"Product"
			)
		),false);
		
		$this->Order->Customer->virtualFields = array(
			"fullname"		=> "CONCAT(Customer.firstname,' ',Customer.lastname)",
		);
		
		if (!empty($custName) && $taskStatus != "all" && $taskStatus != "1") {
			$con = "1 woy";
			$conditions			=	array(
									"Order.delivery_type_id"	=>	"2",
									"Order.receiver_name LIKE"		=>	"%".$custName."%",
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
									"Order.pickup_status"		=>	$taskStatus
								);
		} elseif ($taskStatus == "all" && !empty($custName)) {
			$con = "2 woy";
			$conditions			=	array(
									"Order.delivery_type_id"	=>	"2",
									"Order.receiver_name LIKE"		=>	"%".$custName."%",
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
								);
		} elseif ($taskStatus == "all" && empty($custName)) {
			$con = "3 woy";
			$conditions			=	array(
									"Order.delivery_type_id"	=>	"2",
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
								);
		} elseif (!empty($custName) && $taskStatus == "1") {
			$con = "4 woy";
			$conditions			=	array(
									"Order.delivery_type_id"	=>	"2",
									"Order.receiver_name LIKE"		=>	"%".$custName."%",
									"Order.pickup_status"			=>	$taskStatus,
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
								);
		} elseif (empty($custName) && $taskStatus == "1") {
			$con = "5 woy";
			$conditions			=	array(
									"Order.delivery_type_id"	=>	"2",
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
									"Order.pickup_status"				=>	$taskStatus
								);
		} else {
			$con = "6 woy";
			$conditions			=	array(
									"Order.delivery_type_id"	=>	"2",
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
									"Order.pickup_status"					=>	$taskStatus
								);
		}
								
		$this->paginate		=	array(
			"Order"	=>	array(
				"order"			=>	"Order.id desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"recursive"		=>	3,
				"conditions"	=>	$conditions
			)
		);
		
		try
		{
			$fData			=	$this->paginate("Order");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['Order']['pageCount'],
						"page"		=>	$this->params['paging']['Order']['page'],
						"totalData"	=>	$this->params['paging']['Order']['count'],
						"nextPage"	=>	$this->params['paging']['Order']['nextPage'],
						"request"	=>	$_REQUEST,
						"con"		=>	$con
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

	function SearchTechnicianJobList()
	{
		$status			=	false;
		$message		=	ERR_03;
		$data			=	null;
		$code			=	"03";
		$user_id		=	$_REQUEST['user_id'];
		$page			=	(empty($_REQUEST['page'])) ? 1 : $_REQUEST['page'];
		$dateFrom		=	$_REQUEST['dateFrom']." "."00:00:01";
		$dateTo			=	$_REQUEST['dateTo']." "."23:59:59";
		$taskStatus 			=	$_REQUEST['taskStatus'];
		$custName 				=	$_REQUEST['custName'];
		$assembly_status		=	$_REQUEST['assembly_status'];
		
		//CHECK USER ID
		$this->loadModel("User");
		$check			=	$this->User->find("first",array(
								"conditions"	=>	array(
									"User.is_admin"	=>	"1",
									"User.aro_id"	=>	array("5","6"),
									"User.id"		=>	$user_id
								)
							));
		
		if(empty($check))
		{				
			$out	=	array(
							"status"	=>	false,
							"message"	=>	"Not authorized",
							"data"		=>	array(),
							"code"		=>	"00",
							"request"	=>	$_REQUEST
						);
			
			$json		=	json_encode($out);
			$this->response->type('json');
			$this->response->body($json);
			return;
		}
		
		
		$this->loadModel("TaskHistory");
		$joins			=	array(
								array(
									"table"			=>	"orders",
									"alias"			=>	"Order",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"TaskHistory.order_id		=	Order.id"
									)
								),
								array(
									"table"			=>	"tasks",
									"alias"			=>	"Task",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"TaskHistory.task_id		=	Task.id"
									)
								),
								array(
									"table"			=>	"order_statuses",
									"alias"			=>	"OrderStatus",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"OrderStatus.id				=	Order.status"
									)
								),
								array(
									"table"			=>	"task_statuses",
									"alias"			=>	"TaskStatus",
									'type'			 => 'LEFT',
									"conditions"	=>	array(
										"TaskHistory.status	=	TaskStatus.id"
									)
								)
							);

		if (!empty($taskStatus)) {
			if (!empty($custName) && $taskStatus != "all" && $taskStatus != "1") {
			$con = "1 woy";
			$conditions			=	array(
									"TaskHistory.employee_id"	=>	$user_id,
									"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")",
									"Order.receiver_name LIKE"		=>	"%".$custName."%",
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
									"Order.delivery_status"		=>	$taskStatus
								);
			} elseif ($taskStatus == "all" && !empty($custName)) {
				$con = "2 woy";
				$conditions			=	array(
										"TaskHistory.employee_id"	=>	$user_id,
										"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")",
										"Order.receiver_name LIKE"		=>	"%".$custName."%",
										"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
									);
			} elseif ($taskStatus == "all" && empty($custName)) {
				$con = "3 woy";
				$conditions			=	array(
										"TaskHistory.employee_id"	=>	$user_id,
										"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")",
										"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
									);
			} elseif (!empty($custName) && $taskStatus == "1") {
				$con = "4 woy";
				$conditions			=	array(
										"TaskHistory.employee_id"	=>	$user_id,
										"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")",
										"Order.receiver_name LIKE"		=>	"%".$custName."%",
										"Order.delivery_status"			=>	$taskStatus,
										"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
									);
			} elseif (empty($custName) && $taskStatus == "1") {
				$con = "5 woy";
				$conditions			=	array(
										"TaskHistory.employee_id"	=>	$user_id,
										"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")",
										"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
										"Order.delivery_status"				=>	$taskStatus
									);
			} else {
				$con = "6 woy";
				$conditions			=	array(
										"TaskHistory.employee_id"	=>	$user_id,
										"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")",
										"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
										"Order.delivery_status"					=>	$taskStatus
									);
			}
		} else {
			if (!empty($custName) && $assembly_status != "all" && $assembly_status != "1") {
			$con = "1 woy";
			$conditions			=	array(
									"TaskHistory.employee_id"	=>	$user_id,
									"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")",
									"Order.receiver_name LIKE"		=>	"%".$custName."%",
									"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
									"Order.assembly_status"		=>	$assembly_status
								);
			} elseif ($assembly_status == "all" && !empty($custName)) {
				$con = "2 woy";
				$conditions			=	array(
										"TaskHistory.employee_id"	=>	$user_id,
										"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")",
										"Order.receiver_name LIKE"		=>	"%".$custName."%",
										"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
									);
			} elseif ($assembly_status == "all" && empty($custName)) {
				$con = "3 woy";
				$conditions			=	array(
										"TaskHistory.employee_id"	=>	$user_id,
										"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")",
										"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
									);
			} elseif (!empty($custName) && $assembly_status == "1") {
				$con = "4 woy";
				$conditions			=	array(
										"TaskHistory.employee_id"	=>	$user_id,
										"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")",
										"Order.receiver_name LIKE"		=>	"%".$custName."%",
										"Order.assembly_status"			=>	$assembly_status,
										"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'"
									);
			} elseif (empty($custName) && $assembly_status == "1") {
				$con = "5 woy";
				$conditions			=	array(
										"TaskHistory.employee_id"	=>	$user_id,
										"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")",
										"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
										"Order.assembly_status"			=>	$assembly_status
									);
			} else {
				$con = "6 woy";
				$conditions			=	array(
										"TaskHistory.employee_id"	=>	$user_id,
										"TaskHistory.id	=	(SELECT MAX(id) FROM task_histories WHERE task_id = TaskHistory.task_id and employee_id = ".$user_id.")",
										"Order.created BETWEEN "."'".$dateFrom."'"." AND "."'".$dateTo."'",
										"Order.assembly_status"			=>	$assembly_status
									);
			}
		}
								
		$this->paginate		=	array(
			"TaskHistory"	=>	array(
				"order"			=>	"TaskHistory.id desc",
				"page"			=>	$page,		
				"limit"			=>	10,
				"joins"			=>	$joins,
				"conditions"	=>	$conditions,
				"fields"		=>	array(
					"TaskHistory.*",
					"Task.task_type_id",
					"Order.*",
					"OrderStatus.*",
					"TaskStatus.*"
				),
				"group"			=>	"TaskHistory.task_id"
			)
		);
		
		try
		{
			$fData			=	$this->paginate("TaskHistory");
		}
		catch(NotFoundException $e)
		{
			$fData		=	array();
		}
		
		
		if(empty($fData))
		{
			$status		=	true;
			$message	=	ERR_02;
			$data		=	array();
			$code		=	"02";
		}
		else
		{
			$status		=	true;
			$message	=	ERR_00;
			$code		=	"00";
			$data		=	$fData;
		}
		
		$out	=	array(
						"status"	=>	$status,
						"message"	=>	$message,
						"data"		=>	$data,
						"code"		=>	$code,
						"pageCount"	=>	$this->params['paging']['TaskHistory']['pageCount'],
						"page"		=>	$this->params['paging']['TaskHistory']['page'],
						"totalData"	=>	$this->params['paging']['TaskHistory']['count'],
						"nextPage"	=>	$this->params['paging']['TaskHistory']['nextPage'],
						"request"	=>	$_REQUEST,
						"con"		=>	$con
					);
		
		$json		=	json_encode($out);
		$this->response->type('json');
		$this->response->body($json);
		if(isset($_GET['debug']) && $_GET['debug'] == "1")
		{
			pr($out);
		}
	}

}
?>
