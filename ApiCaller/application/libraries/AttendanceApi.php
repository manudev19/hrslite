<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

trait MatrixKey
{
    public function getAuthentication()
    {
        $content = null;
        $file = new SplFileObject("E:\\wamp\\www\\ApiCaller\\application\\third_party\\matrix_key.txt");
        while(!$file->eof())
        {
            $content .= $file->current();
            $file->next();
        }
        return $content;
    }
}

class AttendanceApi 
{
    use MatrixKey;
    public function templateData($date_range)
    {

        $data_array = array();
    
        $getDate = str_replace("-","",$date_range);
        $dateRange = $getDate."-".$getDate;

        $reqUrl = "http://172.16.1.100/cosec/api.svc/v2/template-data?action=get;id=107;date-range=".$dateRange.";format=json;";
        
     
        $curl = curl_init();
        $options_array = array(
            CURLOPT_URL => $reqUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array("Authorization: ".$this->getAuthentication())
            );

        if(curl_setopt_array($curl,$options_array))
        {
            $response = curl_exec($curl);
            $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) 
            {
                echo "cURL Error #:" . $err;
            } 
            else 
            { 
                $data_array['data'] = json_decode($response,TRUE);
                $data_array['response_code'] = $response_code;
                return json_encode($data_array);
            }
        }
        else
        {
            $data_array['data'] = null;
            $data_array['response_code'] = $response_code;
            return json_encode($data_array);
        } 
    }

    
}