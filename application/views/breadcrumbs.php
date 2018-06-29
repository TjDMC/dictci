<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$segments = $this->uri->segment_array();

//custom url names
$urlnames = array(
    'leaveapplication'=>'Leave Application'
);

$breadcrumbs = '<a href="'.base_url().'">Home</a>';
if(count($segments)>0){
    $ci = get_instance($this);
    $className = get_class($ci);
    $breadcrumbs .=' / <a href="'.base_url().$segments[1].'">'.$className.'</a>';
    $rc = new ReflectionClass($className);
    //Remove class from segments
    array_splice($segments,0,1);

    //iterate through remaining segments
    foreach($segments as$segment){
        if($rc->hasMethod($segment)){
            $segmentNameString = '';
            if(isset($urlnames[strtolower($segment)])){
                $segmentNameString = $urlnames[strtolower($segment)];
            }else{
                $segmentNameArray = preg_split('/_/',$segment);
                foreach($segmentNameArray as $segmentName){
                    $segmentNameString.=ucfirst($segmentName);
                }
            }
            $breadcrumbs.=' / <a href="'.base_url().strtolower($className)."/".$segment.'">'.$segmentNameString.'</a>';
        }else{
            $breadcrumbs.=' / '.$segment;
        }
    }
}
?><!DOCTYPE html>

<?php if ($this->ion_auth->logged_in()):?>
    <div class="card mb-3">
        <div class="card-header">
            <?=$this->ion_auth->logged_in()?$breadcrumbs:''?>
        </div>
    </div>
<?php endif?>
