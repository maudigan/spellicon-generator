<?php
//process governor 
class pid {

    protected $filename;
    public $already_running = false;
    public $id = 0;
   
    function __construct($directory) {
       
        $this->filename = $directory . '/' . basename($_SERVER['PHP_SELF']) . '.pid';
       
        if(is_writable($this->filename) || is_writable($directory)) {
           
            if(file_exists($this->filename)) {
                $this->id = (int)trim(file_get_contents($this->filename));
                if(posix_kill($this->id, 0)) { //check if its running
                    $this->already_running = true;
                }
            }
           
        }
        else {
            die("Cannot write to pid file '$this->filename'. Program execution halted.n");
        }
       
        if(!$this->already_running) {
            $this->id = getmypid();
            file_put_contents($this->filename, $this->id);
        }
       
    }
    
    public function kill() {
      if($this->already_running) {
         posix_kill($this->id, 9); //9 is the halt signal
   
      }
    }
    

    public function __destruct() {

        if(!$this->already_running && file_exists($this->filename) && is_writeable($this->filename)) {
            unlink($this->filename);
        }
   
    }
   
}

//simple count function that acts
// like the one prior to php 7.4
function count_ez($target)
{
   //is_countable added in 7.4 to work with the new version
   //of count. if it doesnt exist, we can safely use the
   //old count.
   if (!function_exists("is_countable")) return count($target);
   
   if (is_countable($target)) 
   {
      return count_ez($target);
   }
   return 0;
}


function formatSeconds($seconds)
{
  $seconds = intval($seconds);
  $hours = 0;
  $milliseconds = str_replace( "0.", '', $seconds - floor( $seconds ) );

  if ( $seconds > 3600 )
  {
    $hours = floor( $seconds / 3600 );
  }
  $seconds = $seconds % 3600;
  
  return str_pad( $hours, 2, '0', STR_PAD_LEFT )
       . gmdate( ':i:s', $seconds )
       . ($milliseconds ? ".$milliseconds" : '')
  ;
}

function set_progress($out) {
   global $progressfile;
   file_put_contents($progressfile, $out);
}

function get_progress() {
   global $progressfile;
   return file_get_contents($progressfile);
}


$tempdir = getcwd().'/temp/';
$outputdir = '/output/';
$progressfile = $tempdir.'progress.txt';

//make our output directories if they dont exist
if (!file_exists($tempdir)) {
    mkdir($tempdir, 0777, true);
}
if (!file_exists(getcwd().$outputdir)) {
    mkdir(getcwd().$outputdir, 0777, true);
}

   
//check if the process is already running
if ($_REQUEST['process'] === 'isrunning') {
   //check if we're running
   $pid = new pid('.');
   
   if($pid->already_running) {
      exit(json_encode(array('STATUS' => 1, 'RUNNINGPID' => $pid->id, 'MYPID' => getmypid())));
   }
   else {
      exit(json_encode(array('STATUS' => 0, 'RUNNINGPID' => $pid->id, 'MYPID' => getmypid())));
   }
   
}

//stop the generation process
elseif ($_REQUEST['process'] === 'killprocess') {
   $pid = new pid('.');
   $pid->kill();
   set_progress("Generation Halted");
   exit();
}

//get the most recent process output
elseif ($_REQUEST['process'] === 'checkprogress') {
      exit(get_progress());
}

//get the list of available downloads
elseif ($_REQUEST['process'] === 'getdownloads') {
   $files_unsorted = glob('output/*.{zip}', GLOB_BRACE);
   asort($files_unsorted);
   $response = <<<RESPONSE
   <table>
      <thead>
         <tr>
            <th>File</th>
            <th>Generated On</th>
         </tr>
      </thead>
      <tbody>
RESPONSE;
   foreach ($files_unsorted as $file) {
      $response .= "<tr><td><a href='./$file'>".basename($file)."</a></td><td>".date("F d Y H:i:s.", filemtime($file))."</td></tr>\r\n";
   }
   $response .= <<<RESPONSE
      </tbody>
   </table>   
RESPONSE;
   exit($response);
}

elseif ($_REQUEST['process'] === 'generate') {
   set_progress("Started...");
   
   //make sure the script doesn't time out
   @set_time_limit(0);
   
   define("ICON_DIM", 40);

   //get the passed parm
   function fetchVar(&$output, $varname, $default) {
      if (isset($_REQUEST[$varname])) {
         $output = $_REQUEST[$varname];
      }
      else {
         $output = $default;
      }
   }
   
   //force int within a range
   function rangeInt(&$val, $min, $max) {
      $val = max($min, min($max, intval($val)));
   }
   
   //fetch user parameters
   fetchVar($outcols, 'cols', 6);
   fetchVar($outrows, 'rows', 6);
   fetchVar($bgr, 'r', 0);
   fetchVar($bgg, 'g', 0);
   fetchVar($bgb, 'b', 0);
   fetchVar($bga, 'a', 127);
   fetchVar($out_first_index, 'imagefilestart', 1);
   fetchVar($icon_count_first, 'iconstart', 500);
   fetchVar($css_filename, 'cssfilename', "spellicons.css");
   fetchVar($css_template, 'csstemplate', ".spellicon-{ICON} { background: url('../spellicons/{FILE}') {X}px {Y}px; }");
   fetchVar($out_file_template, 'imagefilename', "spellsprites_{FILENUM}");
   fetchVar($out_file_type, 'filetype', "png");
   
   //validate user input
   rangeInt($outcols, 1, 101);
   rangeInt($outrows, 1, 101);
   rangeInt($bgr, 0, 255);
   rangeInt($bgg, 0, 255);
   rangeInt($bgb, 0, 255);
   rangeInt($bga, 0, 127);
   rangeInt($out_first_index, 0, 999999);
   rangeInt($icon_count_first, 0, 999999);
   if (preg_match('/[^a-z_\-0-9\.]/i', $css_filename)) exit("ERROR: Output CSS Filename contains invalid characters");
   if (preg_match('/[^a-z_\-0-9\.\{\}\ \/\';:\(\)]/i', $css_template)) exit("ERROR: Output CSS Template contains invalid characters");
   if (preg_match('/[^a-z_\-0-9\.\{\}]/i', $out_file_template)) exit("ERROR: Output Images File Name Template contains invalid characters");
   $validExt = array('png', 'gif', 'jpeg');
   if (!in_array($out_file_type, $validExt))  exit("ERROR: Output Images File Type is not valid.");
   
   
   $outputHeader = <<<TPL
<br><br>
Output Image: %s x %s<br>
Background RGBA: %s, %s, %s, %s<br>
First Image File Name: %s.%s<br>
CSS Filename: %s<br>
CSS Icon Start: %s<br>
CSS Template: %s<br>
TPL;

   $outputHeader = sprintf(
      $outputHeader,
      $outrows,
      $outcols,
      $bgr,
      $bgg,
      $bgb,
      $bga,
      str_replace("{FILENUM}", $out_first_index, $out_file_template),
      $out_file_type,
      $css_filename,
      $icon_count_first,
      $css_template
   );

   //DECLARE VARS
   $outwidth = $outcols * ICON_DIM;
   $outheight = $outrows * ICON_DIM;
   $tempfilename = $tempdir.'temp_convert.png';
   $out_x_pos = 0;
   $out_y_pos = 0;
   $out_count = $out_first_index;
   $icon_count = $icon_count_first;
   $out_image = 0;
   $out_css = "";
   $output_files = array();
   $output_filename_first = 0;
   $thisurl = dirname('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
   
   //only run 1 at a time of this script
   //if it's already running report the status
   $pid = new pid('.');
   if($pid->already_running) {
      exit(json_encode(array('STATUS' => 1)));
   }
   
   
   set_progress("Scanning spelliconsheets directory for source icons.".$outputHeader);

   //figure out which spell icons we are going to process
   //by examining the spelliconsheets directory. We're assuming
   //there isn't a bunch of garbage in the directory
   $files_unsorted = glob('spelliconsheets/*.{tga}', GLOB_BRACE);
   
   /*$files_unsorted = array(
      'spelliconsheets/Spells01.tga',
      'spelliconsheets/Spells02.tga');*/

   //ERROR CHECK SPELLICONSHEETS FILE LIST
   //put them into index array and
   //filter out files without an
   //index
   $files = array();
   $max_index = 0;
   $min_index = 99999;
   foreach ($files_unsorted as $file) {
      preg_match_all('!\d+!', $file, $number);
      $index = $number[0][0];
      
      //if it has an index, add it to new array
      if (is_numeric($index)) {
         $files[$index] = $file;
         $max_index = max($max_index, $index);
         $min_index = min($min_index, $index);
      }
   }

   //make sure its in order
   ksort($files);
   
   
   //sub to save image
   function save_image() {
      global $out_file_type;
      global $out_image;
      global $output_filepath;
      global $output_files;
      global $output_filename;
      global $output_filename_first;
      if ($out_file_type == 'png') {
         imagepng($out_image, $output_filepath);
      }
      elseif ($out_file_type == 'jpeg') {
         imagejpeg($out_image, $output_filepath);
      }
      elseif ($out_file_type == 'gif') {
         if ($bga == 127) {
            imagecolortransparent($out_image, $bgcolor); 
         }
         imagegif($out_image, $output_filepath);
      }
      $output_files[] = $output_filename;
      if (!$output_filename_first) $output_filename_first = $output_filename;
      ImageDestroy($out_image);
      $out_image = 0;
   }

   //if we have any voids, report them to the user so they
   //can replace the missing files
   if ($max_index != count($files))
   {
      $textout = "You are missing some spell icon files! Scan the files below and replace the missing file in the ./spelliconsheets/ directory<br/><br/>";
      for ($i = 1; $i <= $max_index; $i++) {
         $textout .= "./spelliconsheets/spelliconsheets".str_pad($i, 2, '0', STR_PAD_LEFT).".tga &#8680; ";
         if (!array_key_exists($i, $files)) {
            $textout .= "<span style='color:red'>MISSING</span><br/>";
         }
         else {
            $textout .= "<span style='color:green'>LOCATED</span><br/>";
         }
      }
      
      set_progress($outputHeader.$textout);
      exit(json_encode(array('STATUS' => 0)));
   }
   
   //how many icons we need to process
   $icon_count_total = count($files) * 36;
   $icon_count_processed = 0;
   
     

   //PROCESS SPELLICONSHEET FILES INTO NEW IMAGES AND CSS  
   $starttime = time();
   $last_output = time();
   foreach($files as $file) {

      //convert this tga image and save as tempfile.png
      exec("convert $file $tempfilename", $outputarray, $retval);

      if ($return == 1)
      {
         set_progress("Failed to convert $file to $tempfilename.".$outputHeader);
         exit(json_encode(array('STATUS' => 0)));
      }
      
      //open then destroy the converted image
      $sourceimage = imagecreatefrompng($tempfilename);
      imagesavealpha($sourceimage, true); 
      unlink($tempfilename);

      //loop through the converted image, cutting out each icon
      //and pasting it into the new output image
      //calculate the CSS along the way
      for ($source_y = 0; $source_y < 240; $source_y += ICON_DIM) {
         for ($source_x = 0; $source_x < 240; $source_x += ICON_DIM) {
         
            //make sure we have an image to paste to, we create a new one
            //when the old one gets full
            if (!$out_image) {
               $out_image = imagecreatetruecolor($outwidth, $outheight);   
               imagesavealpha($out_image, true);    
               $bgcolor = imagecolorallocatealpha($out_image, $bgr, $bgg, $bgb, $bga);  
               imagefill($out_image, 0, 0, $bgcolor);  
               //if its a gif we dont have an alpha layer
               //so set a palet transparency if they went with 127(full transparent)
               if ($out_file_type == 'gif' && $bga == 127) {
                  imagecolortransparent($out_image, $bgcolor); 
               }
               $output_filename = str_replace("{FILENUM}", $out_count++, $out_file_template).".".$out_file_type;
               $output_filepath = $tempdir.$output_filename;
            }

            //cut from the spell icon and paste to our new sprite sheet
            imagecopy($out_image, $sourceimage, $out_x_pos, $out_y_pos, $source_x, $source_y, ICON_DIM, ICON_DIM);
            
            
            
            $icon_count_processed++;
            //only output updates every 4 seconds (commented out cause it doesn't improve performance)
            //if (time() - $last_output > 24 || $last_output == 0) 
            {
               //$last_output = time();
               
               //estimate time left
               $time_elapsed = time() - $starttime;
               $percent_done = $icon_count_processed/$icon_count_total;
               $percent_done_clean = intval($percent_done*100);
               $time_left = $time_elapsed/$percent_done - $time_elapsed;
               
            
               //mark our progrss in the progress file
               $progress_output = "<div class='progress-bar'><div style='width: $percent_done_clean%;'><span>$percent_done_clean%</span></div></div>";
               $progress_output .= "Progress: ".$icon_count_processed." of ".$icon_count_total." icons processed.<br />";
               $progress_output .= "Time Elapsed: ".formatSeconds($time_elapsed)."<br />";
               $progress_output .= "Time Remaining: ".formatSeconds($time_left)."<br />";
               
               set_progress($progress_output.$outputHeader);
            }
            
            
            //generate the css line for this icon
            $out_css_line = str_replace(array("{ICON}", "{FILE}", "{X}", "{Y}"), array($icon_count++, $output_filename, $out_x_pos, $out_y_pos), $css_template);
            $out_css .= $out_css_line."\r\n";
            
            //calculate the new location on our sprite sheet (or start a new one)
            $out_y_pos += ICON_DIM;
            if ($out_y_pos >= $outheight) {
               $out_y_pos = 0;
               $out_x_pos += ICON_DIM;
               
               if ($out_x_pos >= $outwidth) {
                  $out_x_pos = 0;
                  save_image();
               }
            }
         }
      }   
      
      //destroy the converted image stored in memory
      ImageDestroy($sourceimage);
   }
   

   //we're done looping through all the source files, need to make sure we
   //don't have any lingering output images that weren't done
   if ($out_image) {
      save_image();
   }

   //save how many images we created
   $out_image_count = count($output_files);
     
   //output the css file  
   $css_filepath = $tempdir.$css_filename;
   file_put_contents($css_filepath, $out_css);
   $output_files[] = $css_filename;

   //GENERATE README MANIFEST FOR THE ZIP
   //start manifest log for the zip file
   $manifest = <<<TPL
This archive was generated using spellicongen. The php source code for it can be found 
at https://github.com/maudigan/spellicon-generator. It allows you to convert the .tga spell 
icon images from Everquest into png of gif, retile them into any number of rows and  
columns (including 1x1), rename and number them in any way, and generate the CSS to
describe the files all in one action. -Maudigan

Assemble Data: %s

Input Files: Spells%s.tga to Spells%s.tga

Output Files: %s to %s
              %s total image files

Output images: %s x %s spells
               %s x %s pixels
               
Background: RGBA(%s, %s, %s, %s)
               
first icon number: %s

Output CSS File: %s
TPL;

   //populate dynamic data into manifest
   $manifest = sprintf(
      $manifest, 
      date("F j, Y, g:i a"),
      str_pad( $min_index, 2, '0', STR_PAD_LEFT ),
      str_pad( $max_index, 2, '0', STR_PAD_LEFT ),
      $output_filename_first,
      $output_filename,
      $out_image_count,
      $outrows,
      $outcols,
      $outrows * ICON_DIM,
      $outcols * ICON_DIM,
      $bgr,
      $bgg,
      $bgb,
      $bga,
      $icon_count_first,
      $css_filename
   );
      


   //GENERATE THE ZIP FILE
   //create zip file
   $zip_filename = "spellicons_".time()."_".$outrows."x".$outcols."_".$out_file_type.".zip";
   $zip_filepath = $outputdir.$zip_filename;
   $zip_filepath_qualified = getcwd().$zip_filepath;
   //delete it if it exists
   if (is_file($zip_filepath_qualified)) unlink($zip_filepath_qualified);
   $zip = new ZipArchive();
   if ($zip->open($zip_filepath_qualified, ZIPARCHIVE::CREATE) !== TRUE) exit("cannot open $zip_filepath_qualified\n");

   //add manifest file
   $zip->addFromString("README.txt", $manifest);
   //add images and css to zip
   foreach($output_files as $output_file) {
      $temppath = $tempdir.$output_file;
      if (!is_file($temppath)) continue;
      $zip->addFile($temppath, $output_file);
   }

   //close the zip out     
   $zip->close();



   //remove the unzipped files
   foreach($output_files as $output_file) {
      $temppath = $tempdir.$output_file;
      unlink($temppath);
   }

   //OUTPUT PAGE
   //notify the user
   $output = "<div class='buttonrow'><a href='$thisurl$zip_filepath'>Download CSS and $out_image_count Images</a></div>";
   $output .= $outputHeader;
   set_progress($output);
}
?>
