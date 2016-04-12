/*
 * Function for auto reply to VRBO Property Email.
 * Get Property Total from LVN Server
 * Send property_id and email body to LVN Server Url i.e cost.php
 * @return json response with property total cost,guest cout,user first name,last name,LVN url link.
 */
function RespondEmail(e) {


    var emailQuotaRemaining = MailApp.getRemainingDailyQuota();
    Logger.log("Remaining email quota: " + emailQuotaRemaining);

    if (emailQuotaRemaining > 0) {
        //serach in gmail inbox any email have subject like "Inquiry from"
        var threads = GmailApp.search("label:unread subject:'Inquiry from'", 0, 200);

        Logger.log(threads.length);

        for (var i = 0; i < threads.length; i++) {

            var tmp,
                message = threads[i].getMessages()[0];
            var client_email = message.getTo();
            var array = client_email.split(",");
            // client_email = array[0];

            restrict_mail = 'rcostello73@gmail.com'; // rick mail id to skip mail  

            var Rick_mail_Found = 0;
            for (var k = 0; k < array.length; k++) {
                var stringPart = array[k];

                if (stringPart.trim() == restrict_mail) {
                    Rick_mail_Found = 1; //found 
                    break;
                }
            }

            client_mail_array = ['cphgsales@gmail.com', 'cphgsales@hotmail.com', 'lvnvac@gmail.com', 'lvnvac@outlook.com', 'lvnvacation@gmail.com', 'pete@cpi-homes.com', 'genknooz5@gmail.com'];
            

            if (Rick_mail_Found != 1) //restrict rick mail 
            {
                for (var j = 0; j < array.length; j++) {

                    var client_email_int = client_mail_array.indexOf(array[j].trim());


                    if (client_email_int != -1) {
                        client_email = array[j].trim();
                        break;
                    }

                }
                
              //  Logger.log(client_email);

                //Logger.log('from email id-'+ message.getFrom());
                // Logger.log(message.getTo());
                var from_email = message.getFrom();

                if (from_email != 'noreply@messages.homeaway.com') {
                    content = message.getPlainBody();

                    // Implement Parsing rules using regular expressions
                    if (content) {
                        tmp = content.match(/#\s*([0-9\s]+)(\r?\n)/);
                        var property_id = (tmp && tmp[1]) ? tmp[1].trim() : 'No property_id';

                        if (property_id > 0) {

                            //send email body to lvn server 

                            var payload = {
                                "email_body": content,
                                "property_id": property_id

                            };

                            var options = {
                                "method": "post",
                                "payload": payload
                            };

                            //  var response =  UrlFetchApp.fetch("https://www.lvnvacationhomerentals.com/cost.php", options);
                            // Logger.log(response);

                            try {
                                var response = UrlFetchApp.fetch("https://www.lvnvacationhomerentals.com/cost.php", options);

                                Logger.log('Response Code: ' + response.getResponseCode());

                            } catch (e) {

                                Logger.log('Failure: ' + e);
                                var response = '{"status":"error"}';
                            }

                            Logger.log(response);

                            var data = JSON.parse(response);

                            if (data.status == "success") {
                                var responsebody = "";

                                responsebody += "Hi " + data.fname + "," + "<br/>"

                                if (data.grandTotal > 0) {
                                    responsebody += "<p>Yes it is available for your exact dates. The total cost would be <b>" + data.grandTotal + "</b></p>.<br>";
                                }
                                responsebody += "<p>The Link below contains 20+ Additional Photos, An Accurate Availability Calendar, Additional Property Details, a Pricing Calculator and Location Map.</p>";
                                responsebody += "<br>";

                                responsebody += "<p>Shortly after you click the property details link below,  choose property ID " + data.lvn_property_number + " by clicking on the property photo and or the green property ID text .</p><br>";

                                responsebody += "<p>Please note that ID " + data.lvn_property_number + " is the same property as  " + data.property_number + "</p><br>";

                                // responsebody +="http://lvnvacationhomerentals.com/";

                                responsebody += "http://www.lvnvacationhomerentals.com/?chki=" + data.start_date + "&chko=" + data.stop_date;

                                responsebody += "</br>";

                                responsebody += "<p>P.S. Don't forget,  most properties are up to 50% less when you select ALL weekdays and the calendar on the http://lvnvacationhomerentals.com/ is THE ONLY ACCURATE CALENDAR.</p><br>";

                                responsebody += "\n";
                                responsebody += "<p>Peter V. Anello<br>";
                                responsebody += "Crystal Properties and Investments Inc<br>";
                                responsebody += "Corporate Broker<br>";
                                responsebody += "Phone (424) 260-7113<br>";
                                responsebody += "Fax  (702) 947-5769<br></p>";


                                //  Logger.log(responsebody); 


                                var matches = client_email.match(/\s*"?([^"]*)"?\s+<(.+)>/);
                                if (matches) {
                                    var name = matches[1];
                                    client_email = matches[2];
                                } else {
                                    var name = "no name";
                                    client_email = client_email;
                                }


                                if (threads[i].getMessageCount() == 1) {

                                    //validate to email
                                    var reg = /^([A-Za-z0-9_\.])+\@([A-Za-z0-9_\.])+\.([A-Za-z]{2,4})$/;

                                    if ((reg.test(client_email) == false)) {
                                        if (reg.test(client_email) == false) {
                                            Logger.log(client_email + '=invalid Client Message');
                                        }
                                        /*
                                         if(reg.test(message.getTo()) == false)
                                         {
                                           Logger.log(message.getTo()+'=invalid TO Message');
                                         } 
                                         */

                                    } else {
                                        threads[i].reply("", {htmlBody: responsebody,from:client_email.trim()});
                                        threads[i].markRead();
                                    }

                                }
                            } //end if(data.status == "success")


                        } //end if(property_id > 0)  

                    } // End if
                } // end  if(from_email != 'noreply@messages.homeaway.com')

            } // skip rick mail id  
            else {
                Logger.log('skip');
                //threads[i].markRead();
            }
        } //end for loop
    } // end  if(emailQuotaRemaining > 0)
    else {
        Logger.log("Remaining email quota: " + emailQuotaRemaining);

    }
    // var threads = GmailApp.search("to:(genknooz5@gmail.com) label:unread subject:'LVN Property'");
    //  GmailApp.markThreadsRead(threads);


} //end function RespondEmail