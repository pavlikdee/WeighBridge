<?php

class Vehicle_Model extends CI_Model {

    public function __construct() {

        parent::__construct();

        $CI = & get_instance();
        $CI->load->database();

        $this->db = $CI->db;
    }
    
    public function show_vehicle_no(){
       $query="select registration_no,'mcd' as type from tbl_mcd_own_vehicle_details UNION select registration_no,'private' as type from tbl_private_vehicle_details";
       $result=$this->db->query($query)->result_array();
       return $result;
    }

    public function show_garbage(){
        $this->db->select('id,garbage');
        $result=$this->db->get_where('tbl_master_garbage')->result_array();
        return $result;
    }

    public function show_zone(){
        $this->db->select('id,zone');
        $result=$this->db->get_where('tbl_master_zone')->result_array();
        return $result;
    }

    public function show_vehicle_detail($vehicleno,$type){
        $result_array = ["status"=>false,"result"=>[]];
        if(!empty($vehicleno) && !empty($type)){
            if($type=='mcd'){
                $this->db->select('mcd.*,zone.zone,fleet.fleetoperator,gar.garbage');
                $this->db->from('tbl_mcd_own_vehicle_details as mcd');
                $this->db->join('tbl_master_zone as zone','mcd.zone_id=zone.id','left');
                $this->db->join('tbl_master_fleetoperator as fleet','mcd.fleet_operator_id=fleet.id','left');
                $this->db->join('tbl_master_garbage as gar','mcd.garbage_id=gar.id','left');
                $this->db->where('registration_no',$vehicleno);
                $result=$this->db->get()->row_array();
                $result_array['status'] = true;
                $result_array['result'] = $result;

            }else{
                $this->db->select('pri.*,agency.agencyname,gar.garbage');
                $this->db->from('tbl_private_vehicle_details as pri');
                $this->db->join('tbl_master_agency as agency','pri.agency_id=agency.id','left');
                $this->db->join('tbl_master_garbage as gar','pri.garbage_id=gar.id','left');
                $this->db->where('registration_no',$vehicleno);
                $result=$this->db->get()->row_array();
                $result_array['status'] = true;
                $result_array['result'] = $result;
            }
        }
        return $result_array;
    }

    public function getvehicleid($vehicleno,$type){
        $tbl=$type=='mcd'?'tbl_mcd_own_vehicle_details':'tbl_private_vehicle_details';
        $this->db->select('id');
        $this->db->from($tbl);
        $this->db->where('registration_no',$vehicleno);
        $result=$this->db->get()->row_array();
        return $result['id'];
    }

    public function getVehicleStatus($vehicleid,$type){
        $generaltypests=$this->getGeneralStatus($vehicleid,$type);
        return $generaltypests;
        //$emptytypests=$this->getEmptyStatus($vehicleid,$type);
    }

    public function getGeneralStatus($vehicleid,$type){
        $this->db->select('*');
        $this->db->from('tbl_vehicle_entry');
        $this->db->where(array('vehicle_id'=>$vehicleid,'vehicle_type'=>$type));
        $this->db->order_by('id','desc');
        $result=$this->db->get()->row_array();
        if(count($result)>0){
            $status= $result['vehicle_in_status']=='IN'?true:$this->getVehicleOutStatus($vehicleid,$type);
    
        }else{
            $status=true;
        }
        return $status;
    }

    public function getVehicleOutStatus(){
        $this->db->select('*');
        $this->db->from('tbl_vehicle_exit_details');
        $this->db->where(array('vehicle_id'=>$vehicleid,'vehicle_type'=>$type));
        $this->db->order_by('id','desc');
        $result=$this->db->get()->row_array();
        if(count($result)>0){
            $currentintime=date('Y-m-d h:i:s',strtotime($result['timestamp']));
            $currentouttime=date('Y-m-d h:i:s',strtotime($result['timestamp']));
            $diff=$currentintime-$currentouttime;
        }else{
            return false;
        }
    }

    public function save_vehicle_entry($data){
        $res = array('status' => true, 'msg' => 'Data Successfully Saved');
        $result = $this->db->insert('tbl_vehicle_entry', $data);
        if (!$result) {
            $res = array('status' => false, 'msg' => 'Data Not Saved');
        }
        return $res;
    }
    public function show_exit_vehicle_no(){
       
        $resultArray = $this->db->query("select max(id) as id,max(vehicle_no) as vehicle_no,max(vehicle_type) as vehicle_type,Max(entry_type) as entry_type from tbl_vehicle_entry where vehicle_in_status='IN' group by vehicle_no union select max(id) as id,max(vehicle_no) as vehicle_no,max(vehicle_type) as vehicle_type,Max(entry_type) as entry_type from tbl_empty_vehicle_entry where vehicle_in_status='IN' group by vehicle_no")->result_array();
        return $resultArray;

    }
    public function save_exit_vehicle($exitVehicleArr=[]){
        $result = array('status' => false, 'msg' => 'Vehicle Not Exit');
        if(!empty($exitVehicleArr)){
            if(!empty($exitVehicleArr['vehicle_no']) && !empty($exitVehicleArr['vehicle_type']) && !empty($exitVehicleArr['vehicle_entry_id']) && $exitVehicleArr['vehicle_entry_id']>0){

                   $this->db->insert("tbl_vehicle_exit",$exitVehicleArr);

                   $exit__vehiicle_id=$this->db->insert_id();
                   if($exit__vehiicle_id>0){

                      $result['status'] =  $this->db->where("id",$exitVehicleArr['vehicle_entry_id']);$this->db->update("tbl_vehicle_entry",["vehicle_in_status"=>"OUT"]);
                        
                      $result['msg'] = "Vehicle Exit Successfully";
                   }

            }
        }
        return $result;

    }

    public function check_vehicle_status($vehicle_no,$vehicle_type){
        $sql = "select max(id) as id,max(vehicle_no) as vehicle_no,max(vehicle_type) as vehicle_type,MAX(vehicle_in_status) as vehicle_in_status,MAX(zone_coming_id) as zone_coming_id,Max(entry_type) as entry_type from tbl_vehicle_entry";
        if(!empty($vehicle_no)){
            $sql.=" where vehicle_no='".$vehicle_no."' " ;
        }
        if(!empty($vehicle_type)){
            $sql.=" and vehicle_type='".$vehicle_type."' " ;
        }
        $sql .="UNION select max(id) as id,max(vehicle_no) as vehicle_no,max(vehicle_type) as vehicle_type,MAX(vehicle_in_status) as vehicle_in_status,MAX(zone_coming_id) as zone_coming_id,Max(entry_type) as entry_type from tbl_empty_vehicle_entry";
        if(!empty($vehicle_no)){
            $sql.=" where vehicle_no='".$vehicle_no."' " ;
        }
        if(!empty($vehicle_type)){
            $sql.=" and vehicle_type='".$vehicle_type."' " ;
        }
        $resultArray =$this->db->query($sql)->result_array();
        if(!empty($resultArray)){
            $idsArr = array_column($resultArray,"id");
            $key = array_search(max($idsArr),$idsArr);
            $result = $resultArray[$key];
            $resultArray =[];
            $resultArray=$result;
        
        }
        return $resultArray;
    }

    public function check_exit_vehicle($vehicle_no,$vehicle_type){
         $sql = "select max(id) as id,max(vehicle_no) as vehicle_no,max(vehicle_type) as vehicle_type,MAX(timestamp) as vehicle_exit_time from tbl_vehicle_exit";
        if(!empty($vehicle_no)){
            $sql.=" where vehicle_no='".$vehicle_no."' ";
        }
        if(!empty($vehicle_type)){
            $sql.=" and vehicle_type='".$vehicle_type."' ";
        }
        $resultArray = $this->db->query($sql)->row_array();
        return $resultArray;
    }
    public function getTripTimeDetails($vehicle_no=null){
        $trip_minutes = 0;
        if(!empty($vehicle_no)){
             $sql = "select max(id) as id,max(triptime) as triptime from tbl_private_vehicle_details where registration_no='".$vehicle_no."' group by registration_no";
             $resultArray = $this->db->query($sql)->row_array();
             if(!empty($resultArray) && count($resultArray)){
                $trip_minutes = $resultArray['triptime'];
             }


        }
        return $trip_minutes;


    }
    public function zoneTimeDetails($zone_coming_id=0){
        $zone_minutes = 0;
       if(empty($zone_coming_id)){
          $result = $this->db->get_where("tbl_master_zone",["zone_id"=>$zone_coming_id])->row_array();
          if(!empty($result)){
            $zone_minutes = $result['zone_time'];
          }
         
       }
       return $zone_minutes;
    }
    public function save_empty_vehicle_entry($dataArr){
        $res = array('status' => true, 'msg' => 'Data Successfully Saved');
        $result = $this->db->insert('tbl_empty_vehicle_entry', $dataArr);
        // echo $this->db->last_query();die;
        if (!$result) {
            $res = array('status' => false, 'msg' => 'Data Not Saved');
        }
        return $res;

    }

     public function getVehicleSlipNo(){
       $slipno=0;
       $sql="select max(t1.slipno) as slipno from(select max(slipno) as slipno from tbl_vehicle_entry union select max(slipno) as slipno from tbl_empty_vehicle_entry) as t1";
       $result=$this->db->query($sql)->row_array();
      
       if(!empty($result)){
        return $result['slipno'];
       }else{
          return $slipno; 
       }
    }

    public function show_vehicle_list(){
        $finalArr=[];
        $sql="Select * from tbl_vehicle_entry UNION Select * from tbl_empty_vehicle_entry";
        $resultArray=$this->db->query($sql)->result_array();
        if(count($resultArray)>0){
            foreach($resultArray as $key=>$value){
                $finalArr[$key]['vehicle_no']=$this->getVehicleNo($value['vehicle_id'],$value['vehicle_type']);
                $outVehicledetails=$this->getOutVehicledetails($finalArr[$key]['vehicle_no'],$value['vehicle_type'],$value['entry_type'],$value['id']);
                $finalArr[$key]['slipno']=$value['slipno'];
                $finalArr[$key]['fleet_agency_name']=$value['vehicle_type']=='MCD'?$value['fleet_operator']:$value['agency'];
                $finalArr[$key]['zone_coming_from']=$this->getZone($value['zone_coming_id']);
                $finalArr[$key]['entry_type']=$value['entry_type'];
                $finalArr[$key]['garbage_category']=$this->getgarbage($value['garbage_type_id']);
                $finalArr[$key]['in_weight']=$value['entry_type']=='Empty Entry'?$value['tare_weight']:$value['gross_weight'];
                $finalArr[$key]['out_weight']=$value['entry_type']=='Empty Entry'?$outVehicledetails['grossweight']:$outVehicledetails['emptyweight'];
                $finalArr[$key]['in_time']=!empty($value['timestamp'])?date('m/d/Y h:i A',strtotime($value['timestamp'])):'';
                $finalArr[$key]['out_time']=!empty($outVehicledetails['timestamp'])?date('m/d/Y h:i A',strtotime($outVehicledetails['timestamp'])):'';
            }
            return $finalArr;
        }
    }

    public function getVehicleNo($vehicleid,$vehicle_type){
        $tbl=$vehicle_type=='MCD'?'tbl_mcd_own_vehicle_details':'tbl_private_vehicle_details';
        $this->db->select('registration_no');
        $result=$this->db->get_where($tbl,array('id'=>$vehicleid))->row_array();
        return $result['registration_no'];
    }

    public function getOutVehicledetails($vehicle_no,$vehicle_type,$entry_type,$vehicle_entry_id){
     $sql="Select max(emptyweight) as emptyweight ,max(grossweight) as grossweight,max(timestamp) as timestamp  from tbl_vehicle_exit where vehicle_no='".$vehicle_no."' and vehicle_type='".$vehicle_type."' and entry_type='".$entry_type."' and vehicle_entry_id='".$vehicle_entry_id."'" ;
     $result=$this->db->query($sql)->row_array();
     return $result;
    }

    public function getZone($zoneid){
        $this->db->select('zone');
        $result=$this->db->get_where('tbl_master_zone',array('id'=>$zoneid))->row_array();
        return $result['zone'];
    }

    public function getgarbage($garbagid){
        $this->db->select('garbage');
        $result=$this->db->get_where('tbl_master_garbage',array('id'=>$garbagid))->row_array();
        return $result['garbage'];   
    }
}

