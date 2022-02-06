<html>
   <head>
      <title>Generate Spell Icon Images</title>
   </head>
   <body>
      <style type="text/css">
      <!--

      * 
      {
         font-size: 12px;
         line-height: 15px;
         padding: 0px;
         border: 0px;
         margin: 0px;
      }
      
      BODY
      {
         background-color: #e5e5e5;
         font-family: Arial, Helvetica, sans-serif;
         width: 1600px;
         
      }
      
      TABLE
      {
         border-collapse: collapse;
         border: 1px solid #aaaaaa; 
         text-align: left;
      }
      
      THEAD
      {
         background-color: #aaaaaa;
         color: #f0f0f0;
      }
      
      TH, TD
      {
         padding: 2px 20px;
      }
      
      H1
      {
         font-size: 14px;
         line-height: 26px;
         font-weight: 600;
         margin-top: 20px;
         margin-bottom: 10px;
         color: #777777;
         /* font-variant: small-caps; */
         letter-spacing: 1px;
      }
      
      H2
      {
         font-size: 12px;
         line-height: 26px;
         font-weight: 600;
         margin-top: 0px;
         margin-bottom: 10px;
         color: #777777;
         /* font-variant: small-caps; */
         letter-spacing: 1px;
      }

      DIV.left
      {
         width: 820px;
      }

      DIV.left,
      DIV.right
      {
         display: inline-block;
      }
      
      DIV.left-top,
      DIV.left-bottom,
      DIV.right
      {
         border: 1px solid #aaaaaa;
         border-radius: 15px;
         vertical-align: top;
         background-color: #f0f0f0;
         margin: 10px;
         padding: 20px;
      }
      
      INPUT
      {
         border: 1px solid #999999;
         padding: 1px 2px;
      }
      
      INPUT.small
      {
         width: 30px;
      }
      
      INPUT.large
      {
         width: 200px;
      }
      
      INPUT.extralarge
      {
         width: 350px;
      }
      
      DIV.output-image,
      DIV.output-css
      {
         border-collapse: collapse;
         border: 1px solid #dddddd; 
         text-align: left;
         border-radius: 5px;
         margin: 10px;
         padding: 10px;
         display: inline-block;
         vertical-align: top;
      }
      
      DIV.output-css
      {
         width: 410px;
      }
      
      DIV.output-image
      {
         width: 260px;
      }
      
      DIV.output-image DIV,
      DIV.output-css DIV
      {
         padding: 5px 0px;
      }
      
      DIV.buttonrow
      {
         text-align: center;
         height: 50px;
      }
      
      
      DIV.buttonrow A
      {
         border: 1px solid #5e6ba3;
         background-color: #5e6ba3;
         color: #ffffff;
         text-decoration: none;
         padding: 2px 15px;
         border-radius: 5px;
      }
      
      DIV.left-bottom TEXTAREA
      {
         width: 750px;
         height: 320px;
      }
      
      
      DIV.progress-bar 
      {
         width: 600px;
         border: 1px solid #999999;
         border-radius: 5px;
         margin: 40px auto;
         padding: 0px;
      }
      
      DIV.progress-bar DIV
      {
         height: 47px;
         background: linear-gradient(180deg, rgb(129, 152, 250) 0px, rgb(94, 107, 163) 100%);
         margin: 0px;
         border-radius: 5px;
         border: 0px;
         position: relative;      }
      
      DIV.progress-bar SPAN
      {
         position: absolute;
         left: 9px;
         top: 16px;
         text-shadow: 1px 1px 1px black, 0px 1px 1px black, -1px 1px 1px black, 1px 0px 1px black, -1px 0px 1px black, 1px -1px 1px black, 0px -1px 1px black, -1px -1px 1px black;
         opacity: 1;
         color: white;
      }
      
      -->
      </style>
      
      <div class='left'>
         <div class='left-top'>
            <h1>Generate Settings</h1>
            <div class='output-image'>
               <h2>Output Images</h2>
               <div>
                  Icon Rows <input class='small' type='text' name='rows' maxlength='3' value='6' autofocus />
                  Icon Cols <input class='small' type='text' name='cols' maxlength='3' value='6' />
               </div>
               <div>First File Index <input class='small' type='text' name='imagefilestart' maxlength='4' value='0' /></div>
               <div>File Name <input class='large' type='text' name='imagefilename' maxlength='256' value="spellsprites_{FILENUM}" /></div>
               <div>
               File Type
               <select name="filetype">
                  <option value="png">png</option>
                  <option value="gif">gif</option>
                  <option value="jpeg">jpeg</option>
               </select>
               </div>
               <div>
                  Background RGBA <input class='small' type='text' name='r' maxlength='3' value='0' />
                  <input class='small' type='text' name='g' maxlength='3' value='0' />
                  <input class='small' type='text' name='b' maxlength='3' value='0' />
                  <input class='small' type='text' name='a' maxlength='3' value='127' />
               </div>
            </div>
            <div class='output-css'>
               <h2>Output CSS</h2>
               <div>Icon Number Start <input class='small' type='text' name='iconstart' maxlength='4' value='0' /></div>
               <div>
                  Template <input type='text' class='extralarge' name='csstemplate' maxlength='256' value=".spellicon-{ICON} { background: url('../spellicons/{FILE}') {X}px {Y}px; }" />
               </div>
               <div>Filename <input class='large' type='text' name='cssfilename' maxlength='50' value='spellicons.css' /></div>
            </div>
            <div class='buttonrow'>
               <a href='#' onclick='tryStartStep1()'>Generate</a>
               <a href='#' onclick='haltGeneration()'>Halt</a>
            </div>
         </div>
         <div class='left-bottom'>
            <h1>Generation Response</h1>
            <div id="error_div">
            </div>
            <div id="output_div">
               Loading...
            </div>
         </div>
      </div>
      <div class='right'>
         <h1>Old Downloads</h1>
         <div id="output_downloads">Loading...</div>
      </div>



      <script type='text/javascript'>
         var output_div = document.getElementById('output_div');
         var error_div = document.getElementById('error_div');
         var output_downloads = document.getElementById('output_downloads');
         var finished = false;
         var canStart = false;
         
         function getValue(name) {
            return document.getElementsByName(name)[0].value
         }
          
         function sleep(milliseconds) {
            const date = Date.now();
            let currentDate = null;
            do {
               currentDate = Date.now();
            } while (currentDate - date < milliseconds);
         }

        
         function httpGetAsync(theUrl, callback) {
            var xmlHttp = new XMLHttpRequest();
            xmlHttp.onreadystatechange = function() {
               if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
                  callback(xmlHttp.responseText);
            }
            xmlHttp.open("GET", theUrl, true); // true for asynchronous 
            xmlHttp.send(null);
            
         }
         
         

         //************************************************
         //          UPDATE DOWNLOADS
         //************************************************   
         function updateDownloads() {
            //request the run status
            console.log("updateDownloads: updating download list.");
            var url = './generate.php?process=getdownloads';
            httpGetAsync(url, updateDownloadsResponse);
         }
         
         function updateDownloadsResponse(response) {
            //update the downloads output
            console.log("updateDownloadsResponse: downloads updated.");
            output_downloads.innerHTML = response;
         }
         
         updateDownloads();
         
         //************************************************
         //          GENERATION STATUS LOOP
         //
         // loop is formed with doProgressLoop, 
         // runCheckResponse and checkProgressResponse
         //************************************************
         function doProgressLoop() {
            //request the run status
            console.log("doProgressLoop: Requesting the run status of the generation script.");
            var url = './generate.php?process=isrunning';
            httpGetAsync(url, runCheckResponse);
         }
         
         function runCheckResponse(response) {
            //get the run status from the response
            var running = JSON.parse(response).STATUS;
            
            //its not running, so it must have finished
            if (running === 0) {
               console.log("runCheckResponse: The generation script seems to have finished. Letting it update the output 1 more time");
               finished = true;
            } else {
               console.log("runCheckResponse: The generation script is still running.");
            }
            
            //update the progress whether its dont or not, the loop doesn't end here
            //it ends in checkProgressResponse
            console.log("runCheckResponse: Checking generation script response.");
            var url = './generate.php?process=checkprogress';
            httpGetAsync(url, checkProgressResponse);
         }   
         
         function checkProgressResponse(response) {
            //update the displayed output
            output_div.innerHTML = response;
            
            if (finished == false) {
               //if its not finished, loop again
               doProgressLoop();
            } else {
               //its finished, reset the control vars
               console.log("checkProgressResponse: The generation script is finished, and the display has been updated, resetting control vars.");
               finished = false;
               canStart = true;
               updateDownloads();
            }
            
         }
         

         
         
         
         

         //************************************************
         //                 START NEW GENERATION
         //************************************************    
         function generateResponse(response) {
            console.log("generateResponse: Start request has been sent.");
            if (response) error_div.innerHTML = response;
         }   
         
         function tryStartStep2(response) {
            var running = JSON.parse(response).STATUS;
            
            if (running === 0) {
               console.log("tryStartStep2: The generation script is not running yet, starting it.");
               var url = './generate.php?process=generate';
               
               //get vars from form and add them to the url
               var formFields = document.getElementsByTagName('input');
               for (const formField of formFields) {
                  url += "&" + formField.name + "=" + encodeURI(formField.value);
               }
               //send the filename select too
               var formField = document.getElementsByName('filetype');
               url += "&filetype=" + encodeURI(formField[0].value);
               
               console.log("tryStartStep2: contacting: " + url);
               httpGetAsync(url, generateResponse);
               //give it a few seconds to get started
               sleep(2000);
            }
            
            finished = false;
            console.log("tryStartStep2: The Start request has been sent to the generation script. Jumping into the progress loop.");
            doProgressLoop();
         }
         
         function tryStartStep1() {
            //clear any previous errors
            error_div.innerHTML = "";
            
            if (!canStart) {
               console.log("tryStartStep1: User tried to start, but its already running, or we haven't finished the initial check.");
               alert("The generation script is already running or the page hasn't finished initializing. Please try again later");
               return;
            }
            canStart = false;
            
            output_div.innerHTML = "Starting Generation.";
            
            //as soon as we load, check if its already started
            console.log("tryStartStep1: user tried to start, checking it its already going first");
            var url = './generate.php?process=isrunning';
            httpGetAsync(url, tryStartStep2);
         }
         
         
         
         
         
         
         //************************************************
         //                 INITIAL CHECK
         //
         // as soon as the page loads check to see if the
         // generations script is already running. If it
         // is, start checking its progress
         //************************************************      
         function checkIfStarted(response) {
            var running = JSON.parse(response).STATUS;
            
            if (running === 0) {
               console.log("checkIfStarted: Did initial check of generation script and it is not started.");
               output_div.innerHTML = "Not running...";
               canStart = true;
               return;
            }
            
            canStart = false;
            finished = false;
            console.log("checkIfStarted: Another user or process is already generating images. Jumping into the progress loop.");
            doProgressLoop();
         }
         
         
         
         //as soon as we load, check if its already started
         console.log("Root: requesting initial run status");
         var url = './generate.php?process=isrunning';
         httpGetAsync(url, checkIfStarted);
         
            
         function haltGeneration() {
            //stop it if it's running
            console.log("haltGeneration: stopping running process");
            var url = './generate.php?process=killprocess';
            httpGetAsync(url, haltResponse);
         }
         
         function haltResponse(response) {
         }
         
      </script>   
   </body>
</html>