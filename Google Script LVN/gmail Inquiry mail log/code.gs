/*
 * Google script for scan all gmail inbox and sent box 
 * Fetch all mail which subject Inquiry from and no any custom lable (google_log) 
 * Fetch information and add to google sheets 
 */
function myFunction() {

    var threads = GmailApp.search("has:nouserlabels subject:'Inquiry from'", 0,10);
   var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("Sheet1");
     Logger.log(threads.length);
    for (var i = 0; i < threads.length; i++) {
        var tmp,
            message = threads[i].getMessages()[0];

        var client_email = message.getTo();
        var array = client_email.split(",");
        client_email = array[0];
        // getBody , getPlainBody
        content = message.getPlainBody();
        
      // Logger.log(content);
      // return false;
        // Implement Parsing rules using regular expressions
        if (content) {
            tmp = content.match(/#\s*([0-9\s]+)(\r?\n)/);
            var property_id = (tmp && tmp[1]) ? tmp[1].trim() : 'No property_id';

            if (property_id > 0) {


                client_mail_array = ['cphgsales@gmail.com', 'cphgsales@hotmail.com', 'lvnvac@gmail.com', 'lvnvac@outlook.com', 'lvnvacation@gmail.com', 'pete@cpi-homes.com', 'genknooz5@gmail.com'];
                for (var j = 0; j < array.length; j++) {

                    var client_email_int = client_mail_array.indexOf(array[j].trim());


                    if (client_email_int != -1) {
                        client_email = array[j].trim();
                        break;
                    }

                }
               // Logger.log(client_email);

                //send email body to lvn server 

                var payload = {
                    "email_body": content,
                    "property_id": property_id,
                    "peter_mail_id": client_email,
                    "user_mail_id": message.getFrom()

                };

                var options = {
                    "method": "post",
                    "payload": payload
                };

                var response = UrlFetchApp.fetch("https://www.lvnvacationhomerentals.com/cost2.php", options);

               //   Logger.log(response);
             // return false;

                var data = JSON.parse(response);

                if (data.status == "success") {

                    Logger.log('******************');
                    var json = response;
                    //Logger.log(response);
                    // return false;
                    /* google sheet code */
                    // var json = {"property_number":"940332","arrival_date":"06\/07\/2016","fname":"Amanda","lname":"Wurster","total_guest":10,"grandTotal":0,"lvn_property_number":"","status":"success"}


                  //  var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("Sheet1");
                    var keys = Object.keys(data).sort();
                   // Logger.log(keys);
                    var last = sheet.getLastColumn();

                    var header = sheet.getRange(1, 1, 1, last).getValues()[0];
                    var newCols = [];

                    for (var k = 0; k < keys.length; k++) {
                        if (header.indexOf(keys[k]) === -1) {
                            newCols.push(keys[k]);
                        }
                    }

                    if (newCols.length > 0) {
                        sheet.insertColumnsAfter(last, newCols.length);
                        sheet.getRange(1, last + 1, 1, newCols.length).setValues([newCols]);
                        header = header.concat(newCols);
                    }

                    var row = [];

                    for (var h = 0; h < header.length; h++) {
                        row.push(header[h] in data ? data[header[h]] : "");
                    }

                    sheet.appendRow(row);

                    /* end google sheet code */
                    var label = GmailApp.getUserLabelByName("google_log");
                    threads[i].addLabel(label);

                    




                } //end if(data.status == "success")


            } //end if(property_id > 0)     
        } //end if(property_id > 0)     

    } //end for loop   
  
           /*****  remove dublicate recoreds *******/

                    var data = sheet.getDataRange().getValues();
                    var newData = new Array();
                    for (i in data) {
                        var row = data[i];
                        var duplicate = false;
                        for (j in newData) {
                            if (row.join() == newData[j].join()) {
                                duplicate = true;
                            }
                        }
                        if (!duplicate) {
                            newData.push(row);
                        }
                    }
                    sheet.clearContents();
                    sheet.getRange(1, 1, newData.length, newData[0].length).setValues(newData);
                    /*****  end remove dublicate records ******/



} //end function doGet