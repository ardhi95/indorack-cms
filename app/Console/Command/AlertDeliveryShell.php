<?php

App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('GeneralComponent', 'Controller/Component');

class AlertDeliveryShell extends AppShell {
	
	public $uses = array(
		'Order',
		'Notification',
		'NotificationGroup',
		'User'
	);
	
	private $settings,$General;
	
	private $MAX_NOTIF	=	3;
	
    public function main()
	{
        $this->out("========= START SHELL===========//");
		$this->out("RUNNING ON ".date("d m Y H:i:s"));
		//SETTING
		$this->settings = Cache::read('settings', 'long');
		if(!$this->settings || (isset($_GET['debug'])))
		{
			$settings			=	$this->Setting->find('first');
			$this->settings		=	$settings['Setting'];
			Cache::write('settings', $this->settings, 'long');
		}
		
		$this->execute();
		$this->out('========= END SHELL===========//');
    }
	
	private function execute()
	{
		$collection		=	new ComponentCollection();
        $this->General	=	new GeneralComponent($collection);
        $controller 	=	new Controller();
        $this->General->initialize($controller);
		
		$this->Order->bindModel(array(
			"belongsTo"	=>	array(
				"Customer"	=>	array(
					"foreignKey"	=>	"customer_id",
					"className"		=>	"User"
				)
			)
		),false);
		
		$this->Order->Customer->virtualFields	=	array(
			"fullname"		=> "CONCAT(Customer.firstname,' ',Customer.lastname)"
		);
		
		$Order			=	$this->Order->find("all",array(
								"conditions"	=>	array(
									"Order.delivery_status"		=>	array("1","4","8"),
									"Order.delivery_type_id"	=>	"1",
									"OR"		=>	array(
										"Order.last_notification_delivery IS NULL",
										"DATE_ADD(NOW(), INTERVAL -".$this->MAX_NOTIF." HOUR) > Order.last_notification_delivery"
									)
								),
								"limit"							=>	1,
								"order"							=>	"Order.id ASC"
							));
		
		
		if(!empty($Order))
		{
			foreach($Order as $dataOrder)
			{
				$lastNotification		=	$dataOrder["Order"]["last_notification_delivery"];
				$created				=	$dataOrder["Order"]["created"];
				
				if(!is_null($lastNotification))
				{
					//CHECK WAKTU TERAKHIR KIRIM NOTIFIKASI
					$diferrentTime		=	(time() - strtotime($lastNotification));
					
					//SETIAP 3 JAM SEKALI KIRIM NOTIFICATION
					if($diferrentTime >= ($this->MAX_NOTIF * 3600))
					{
						$allowedStartSend	=	mktime(8,0,0,date("n"),date("j"),date("Y"));
						$allowedEndSend		=	mktime(22,0,0,date("n"),date("j"),date("Y"));
						$now				=	time();
						
						//JIKA SAAT INI BERADA PADA JAM YANG DIPERBOLEHKAN UNTUK MENGIRIM NOTIFIKASI
						if($now >= $allowedStartSend && $now <= $allowedEndSend)
						{
							//UPDATE TERAKHIR KALI KIRIM NOTIFIKASI
							$this->Order->updateAll(
								array(
									"last_notification_delivery"	=>	"'".date("Y-m-d H:i:s")."'"
								),
								array(
									"Order.id"	=>	$dataOrder["Order"]["id"]
								)
							);
							
							//SEND NOTIFICATION
							$this->SendNotification($dataOrder);
						}
					}
				}
				else
				{
					//CHECK WAKTU PEMBUATAN ORDER
					$diferrentTime		=	(time() - strtolower($created));
					if($diferrentTime 	>= (3*3600))//3 JAM DARI ORDER DI BUAT
					{
						//UPDATE TERAKHIR KALI KIRIM NOTIFIKASI
						$this->Order->updateAll(
							array(
								"last_notification_delivery"	=>	"'".date("Y-m-d H:i:s")."'"
							),
							array(
								"Order.id"	=>	$dataOrder["Order"]["id"]
							)
						);
						
						//SEND NOTIFICATION
						$this->SendNotification($dataOrder);
					}
				}
			}
		}  
	}
	
	function SendNotification($detailOrder)
	{
		//=========== SAVE NOTIFICATION ================//
		$listKepalGudang	=	$this->User->find("list",array(
									"conditions"	=>	array(
										"User.aro_id"	=>	4
									),
									"fields"		=>	array(
										"User.id",
										"User.gcm_id"
									)
								));
		
		if(!empty($listKepalGudang))
		{
			$arrGcmId		=	array();
			$title			=	'INDORACK';
			
			$message    	=	"Order ".$detailOrder["Order"]['delivery_no']." has not assigned to driver yet!";
			$description   	=	"Delivery No. : ".$detailOrder["Order"]['delivery_no']."<br/>Customer : ".$detailOrder['Customer']['fullname']." has not assigned to driver yet!";
			$created		=	date("Y-m-d H:i:s");
			$tab_index		=	0;
			
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
			
			foreach($listKepalGudang as $idKplGdng =>$gcm_id)
			{
				$this->Notification->create();
				$Notif["Notification"]["user_id"]					=	$idKplGdng;
				$Notif["Notification"]["gcm_id"]					=	empty($gcm_id) ? NULL : $gcm_id;
				$Notif["Notification"]["notification_group_id"] 	=	$notificationGroupId;
				$Notif["Notification"]["order_id"]					=	$detailOrder["Order"]["id"];
				$Notif["Notification"]["title"]						=	$title;
				$Notif["Notification"]["params"]					=	json_encode(array(
																			array(
																				"key"	=>	"id",
																				"val"	=>	"1"
																			),
																			array(
																				"key"	=>	"task",
																				"val"	=>	"2"
																			),
																			array(
																				"key"	=>	"currentTabIndex",
																				"val"	=>	$tab_index
																			)
																		));
				$Notif["Notification"]["message"]					=	$message;
				$Notif["Notification"]["description"]				=	$description;
				$Notif["Notification"]["android_class_name"]		=	'DashboardKepalaGudang';
				$Notif["Notification"]["created"]					=	$created;
				
				if(!empty($gcm_id))
					$arrGcmId[]								=	$gcm_id;
				$this->Notification->save($Notif,array("validate"=>false));
			}
			
			$res 						=	array();
			$res['data']['title'] 		=	$title;
			$res['data']['message'] 	=	$message;
			$res['data']['class_name'] 	=	'DashboardKepalaGudang';
			$res['data']['params'] 		=	array(
												  array(
													  "key"	=>	"id",
													  "val"	=>	"1"
												  ),
												  array(
													  "key"	=>	"task",
													  "val"	=>	"2"
												  ),
												  array(
													  "key"	=>	"currentTabIndex",
													  "val"	=>	$tab_index
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
		//=========== SAVE NOTIFICATION ================//
	}
}
?>