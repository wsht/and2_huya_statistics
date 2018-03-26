/**
 * Created by hantong on 18/3/15.
 */

const process = require("process");
let character = process.argv[2];
let roomid = process.argv[3];

if(!character || !roomid){
  console.log(`error character: ${character} or roomid ${roomid}`);
  return;
}


console.log("start listen "+character + " " + roomid + "\n");

const huya_danmu = require('huya-danmu')
// const roomid = '2058731947'
const client = new huya_danmu(roomid)
const fs = require("fs");

const log_dir = '/data/huya_log/v1';
const message_log  = log_dir + "/message/";
const online_log = log_dir + "/online/";
const gift_log = log_dir + "/gift/"

const msgLogName = (pre) => {
  let date = new Date();

  let month = (date.getMonth() + 1).toString().padStart(2, 0);
  let day = (date.getDate()).toString().padStart(2, 0);

  let logName = `${character}-${roomid}.${date.getFullYear()}-${month}-${day}.log`;

  return `${pre}${logName}`;
}


client.on('connect', () => {
  console.log(`已连接huya ${roomid}房间弹幕~ at time ${(new Date()).getTime()}`)
})


client.on('message', msg => {

  switch (msg.type){
    case "chat":
      fs.appendFile(msgLogName(message_log), JSON.stringify(msg) + "\n", (err) => {
        if (err)
          console.log(err);
      });
      break;
    case "gift":
      fs.appendFile(msgLogName(message_log), JSON.stringify(msg) + "\n", (err) => {
        if (err)
          console.log(err);
      });
      break;
    case "online":
      fs.appendFile(msgLogName(online_log), JSON.stringify(msg) + "\n", (err) => {
        if (err)
          console.log(err);
      });
    default:
      break;
  }

})

client.on('error', e => {
  console.log("error at time" + (new Date()).getTime() + "\n");
  console.log(e);
  console.log("\n");
})

client.on('close', () => {
  console.log("close at time" + (new Date()).getTime() + "\n");
  console.log("\n");
})

client.start()
