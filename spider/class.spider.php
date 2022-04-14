<?php /* EL PSY CONGROO */        			 
class ZBlogSEO extends Base    					            	 	
{    	   	  	      		 			
    private $spiders= null;    		 		        		 		 	
    private $spider=null;    			    	    	 	 				
    private $Max_row=100000;    	 	 	 	       	 				
    public  $url_format ='';    		  	       	 	 		 	
    public $template =null;    	 	 		 	    	 		   	
    public $select='*';     						     	     	 
    public $where =array();     		  	      		 			 	
    public $order = null;    		  		 	      	  	 	
    public $limit=null;    			    	    	 	 	  	
    function __construct($action = null)     					      	  			  
    {    				   	    								
        global $zbp;     		   	     	  			 	
        parent::__construct($zbp->table['ZBlogSEO'], $zbp->datainfo['ZBlogSEO']);    		 		 		    		 			 	
    	   				    	  		 		
        $this->spiders = explode('|', $zbp->Config('ZBlogSEO')->spiders);    	  	   	         			
        foreach ($this->spiders as $key => $spider) {    		 	 	 	       	   	
            $spidername = explode(',', $spider);    	   		      	  	  		
            $this->spider[$spidername[0]] = $spidername[1];    	  			      			    	
        }    	   			      	 					
     	    		     	    	 
        if ($action =='Load') {    		 	 		     			 	   
            $this->Load();    			  	       					  
        }       					    	 	 	 	 
    	 	 	 		     	 		   
        $this->DateTime = time();    	 						        	   
    }      	  	 	     	   	  
    	 	 	 		    	 	   	 
    function  __get($name){    	 		  		    	   		 	
        switch($name){       	 	 	    								
            case 'Url':    							       	 		  
                return htmlspecialchars($this->data['Url']);     		 	 		           	
            case 'url_txt':    	  		       	 						
                return substr($this->Url,0,70);      	  		      	 	 	 	
            default:    			  	 	     	   		 
                return parent::__get($name);      	 	 	     			   	 
        }    	           	  	  	 
    }    		 	 			    				 	 	
           	    	    	  
    function Load()                     			
    {    	 						       	   	
        global $zbp;      	    	    	   		  
    	 		  	       		   	
        $this->template = new Template();    	     	      	 	  		
        $this->template->SetPath(dirname(__FILE__) . DIRECTORY_SEPARATOR.'tp'.DIRECTORY_SEPARATOR);      		 	 	    	   			 
        if(isset($zbp->templatetags)){       	 	 	    			  			
            $this->template->SetTagsAll($zbp->templatetags);    		   		     				 	 	
        }else{    	 		 		      		  			
            if(is_array($zbp->template->templateTags))     			  	     		 		  	
                $this->template->SetTagsAll($zbp->template->templateTags);     	   		        	 			
        }      		  		    	  	 		 
      						     			   	
    	    	        						
        $this->order=array($this->datainfo['ID'][0]=>'DESC');    	    	 	    	  			 	
    }     	 			      					   
    	     	      	  	   
    function InsertLog($HttpStatus=200) //记录爬行     	 	   	    	 				  
    {     	          						 	
        global $zbp;      			 	        				 
    	  	        	 	 	  	
        $agent = null;       	           				 
        $status = $HttpStatus;      	 				    	   		 	
        $url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 				      			    
        if($zbp->Config('ZBlogSEO')->cdnip){     	 	 	       		 	 		
			$ip = GetVars("HTTP_X_FORWARDED_FOR", "SERVER");        				    		 			 	
		}else{      	  			    	  	  	 
			$ip = GetVars("REMOTE_ADDR", "SERVER");    	  			       	  	 		
		}         							
		      		   	
        $datetime = time();     	  	 		    			 		  
        $spiders = explode('|', $zbp->Config('ZBlogSEO')->spiders);     		   		    			 	 	 
    		  				    	 	 		 	
        foreach ($this->spiders as $key => $spider) {        			         			 
     	  	 		    	 			   
            $spidername = explode(',', $spider);       	 		      		 				
            if(strpos(GetGuestAgent(), $spidername[0]) !== false) {       	 	      	  	   	
                $agent = $spidername[1];     	     	     			  		
                break;    		 	         		 	  	
            }    		 				       	 			 
        }    	 			        	 			  
    				   	     	 	 		 
    	 	 	  	         	 	
        if($url && $agent) {    	 		  	     	   	   
            $array = array('Spider_Name' => $agent, 'Spider_IP' => $ip, 'Spider_DateTime' => $datetime, 'Spider_Url' => $url, 'Spider_Status' => $status);        	 	          	  
            $sql = $zbp->db->sql->Insert($this->table, $array);     			  		     							
            $zbp->db->Insert($sql);    					  	       			  
            $this->Auto_del();    			  		       		 			
        }    	 			 		    	 				 	
      	 		 	     			  		
    }     		 		 	    				  	 
       	  		     	 		  	
    function GetData($limit = null) //拉取爬行记录     	   	       				 		
    {     	  	 	     						  
    				  	        	 		 
        global $zbp;     		 			     	   			 
        $_name =GetVars('name','GET');    	  	 	 	    	 	   	 
        $_ip =GetVars('ip','GET');    	 	  			    					  	
        $p = new Pagebar($this->url_format,false);    			 			     			 				
        $p->PageCount = $zbp->Config('ZBlogSEO')->viewconut;    	 	  	       		  			
        $p->PageNow = (int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');     		           		 			
        $p->PageBarCount = $zbp->pagebarcount;    	 	         	 	   		
    	 			  	      		    
        $this->limit = array(($p->PageNow-1) * $p->PageCount,$p->PageCount);    	      	    	 	 	   
        $option = array('pagebar'=>$p);    	  	  	     	 	 	  	
        $limit = $limit==null?$this->limit : $limit;    	 	 	  	      	 	 	 
    	 	 				     	   			
        if($_name<>null ){$where[]=array('=','Spider_Name',$_name);}    	     		      		 			
        if($_ip<>null ){$where[]=array('=','Spider_IP',$_ip);}    	 		  		    		    	 
     			         	  				
        $sql = $zbp->db->sql->Select($zbp->table['ZBlogSEO'], $this->select, $this->where, $this->order, $limit, $option);    		 	 	 	        	  	
        #echo $sql ;exit;    				 	 	      					 
        $array['data']= $this->GetListType('ZBlogSEO',$sql);       	  	     	 		  		
        $array['pagebar']=$p;      	 		 	     	   	  
        return $array;    			 		      				  	 
     			  	      		 		 	
    }    	  		 		    		   	  
     		  	 	      	   		
    function GetListByURL($limit = null) //受访排行      		   	      	  		 
    {      		 			     	  				
        global $zbp;      		 	 	    	 			 		
        $limit =is_numeric($limit)?"LIMIT 0 ,".$limit :'';
        $sql = "SELECT Spider_Url, COUNT(*) as Nums
        FROM {$this->table}
        GROUP BY  Spider_Url
        ORDER BY Nums DESC
        $limit";
     			  		    		 	 		 
        return $array = $this->GetListType('ZBlogSEO',$sql);    		   		       	 	 		
        return $zbp->db->Query($sql);     		 				     					  
    		 			 	      	 		  
    }    	  			 	    	 	 	  	
    				 	 	      			 		
    function GetListByName($limit = null) //来访排行     		  		        	 	  
    {      	 				    				 	 	
        global $zbp;       	 		         	 	 
    	 		  	     			  	 	
        $limit =is_numeric($limit)?"LIMIT 0 ,".$limit :'';
        $sql = "SELECT Spider_Name, COUNT(*) as Nums
        FROM {$this->table}
        GROUP BY  Spider_Name
        ORDER BY Nums DESC
        $limit";
    	      	    								
        return $array = $this->GetListType('ZBlogSEO',$sql);         	      	  		 		
        return $zbp->db->Query($sql);        	 		     	 					
    	 	 	 	      					  
    }     					 	    	 			 	 
      		         							
    function GetListByERR($limit = null) //错误排行     	 		         					 
    {       		 	     			 		 	
        global $zbp;          		    	  					
        $limit =is_numeric($limit)?"LIMIT 0 ,".$limit :'';
        $sql = "SELECT Spider_Status, COUNT(*) as Nums , Spider_Url
        FROM {$this->table}
        WHERE Spider_Status <>  '200'
        GROUP BY Spider_Status ,  Spider_Url
        ORDER BY Nums DESC
        $limit";
    			 		 	       	   	
        return $array = $this->GetListType('ZBlogSEO',$sql);    		 		 		    	 			  	
        return $zbp->db->Query($sql);       	 	       			   	
    }    		    		          	 
    			 		 	     	    	 
    function GetListLast($limit = null) //上次来访时间      	 				    	 		 	 	
    {    		 	         	  	 	 
        global $zbp;     		 		        			   
        $limit =is_numeric($limit)?"LIMIT 0 ,".$limit :'';
        $sql = "SELECT A.* FROM {$this->table} A,
        (SELECT Spider_Name,max(Spider_ID)  maxID FROM {$this->table} GROUP BY Spider_Name) B
        WHERE A.Spider_ID = B.maxID
        ORDER BY A.Spider_DateTime DESC
        $limit";
    				 	 	    	  	    
        return $array = $this->GetListType('ZBlogSEO',$sql);    	 		  	      		 	 		
        return $zbp->db->Query($sql);    	    	 	    		  	  	
    }    		 			      	 	  		 
       		        	 	  	 
    function GetListToday($date = null,$limit = null) //上次来访时间    	 		 	 	    					 	 
    {                	 	  	 	
        global $zbp;     				  	     		  		 
        $date = $date==null?date('Y-m-d',time()):$date;      		  		    				    
        $today = strtotime($date);    				 		     				 	  
        $tomorrow =strtotime('+1 day',$today);      					     	   	 		
    					        	 		 	 
        $limit =is_numeric($limit)?"LIMIT 0 ,".$limit :'';
        $sql = "SELECT A.Spider_Name,A.Spider_IP,A.Spider_DateTime, B.Nums FROM {$this->table} A,
        (SELECT Spider_Name,MIN(Spider_ID)  minID,COUNT(*) AS Nums FROM {$this->table} WHERE Spider_DateTime >= {$today} AND Spider_DateTime <={$tomorrow} GROUP BY Spider_IP ) B
        WHERE A.Spider_ID = B.minID
        ORDER BY A.Spider_DateTime ASC
        $limit";
    #echo $sql;echo $today;echo date('Y-m-d H:i:s',$today);exit;     		  	       	 		 		
        return $array = $this->GetListType('ZBlogSEO',$sql);        	  	    			 	 		
        return $zbp->db->Query($sql);     			  	     	 	 	   
    }      		   	    	 	 		  
      	  			    	   			 
    function Time($s='Y-m-d H:i:s'){      	 	  	    			 				
        return date($s,(int)$this->DateTime);      		 	 	    			 				
    }    		 	  	     	   			 
    	 			       	    	 	
    function Auto_del($id='x'){    		 				     		 		 	 
        global $zbp;    					 	       	 		 	
     	    		    				 	 	
        if($id<>'x'){     	   			     		 		  
            return;    		 		        						 
            $sql = $zbp->db->sql->Delete($this->table, array(array('=', 'ID', $id)));    		   	 	     		 	   
            $zbp->db->Delete($sql);     			 	 	    	   		 	
        }      	    	    	   		  
     	 		  	     	  	  	
        $reust_conut=$zbp->db->sql->Count($this->table,array(array('COUNT', '*', 'num')),'');     			        	 	  	 	
        $reust_conut = GetValueInArrayByCurrent($zbp->db->Query($reust_conut), 'num');    	 		  		       		 		
    	 				      		    	 
        if ($reust_conut > $this->Max_row){    		    	     	  		 	 
            $array=$zbp->db->sql->Select($this->table,'*', array(), array('Time' => 'ASC'),1, '');    	 		 			    	 		 		 
            $sql = $zbp->db->sql->Delete($this->table, array(array('>=', 'DateTime', time()-(60*60*24*$zbp->Config('ZBlogSEO')->logdate))));      	  	 	    		    		
            $zbp->db->Delete($sql);     		   		    		    		
        }        	         	   		
    }    		 		       		 	 	  
       		 		    		 	  	 
    function GetListType($type,$sql)    			   		    		     	
    {      				 	       	    
        global $zbp,$zbpvers;    		 	  		    	  					
        if(array_key_exists('150101',$zbpvers)) {    		 			 	    			  			
            return $zbp->GetListType($type, $sql);       		 		    				 	  
        }else{    		  	         	  	  
            return $zbp->GetList($type, $sql);     				  	     	     	
        }     	   	        		   	
    }          	     	   	 	 
    					 		      	 			 
        		 	    	  			 	
}    	  		  	    		      
     		 	    					   
			     		 				