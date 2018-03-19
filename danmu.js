/**
 * Created by hantong on 18/3/15.
 */

const huya_danmu = require('huya-danmu')
const roomid = '2058731947'
const client = new huya_danmu(roomid)
const fs = require("fs");

const msgLogName = () => {
  let date = new Date();

  return `./message.${date.getFullYear()}-${date.getMonth()+1}-${date.getDate()}.log`
}

client.on('connect', () => {
  console.log(`已连接huya ${roomid}房间弹幕~ at time ${(new Date()).getTime()}`)

})

client.on('message', msg => {
  fs.appendFile(msgLogName(), JSON.stringify(msg) + "\n", (err) => {
    if (err)
      console.log(err);
  });
  // switch (msg.type) {
  //   case 'chat':
  //     console.log(`${msg.id}`);
  //     console.log(`${msg.from.rid}`);
  //     console.log(`[${msg.from.name}]:${msg.content}`)
  //     break
  //   case 'gift':
  //     console.log(`[${msg.from.name}]->赠送${msg.count}个${msg.name}`)
  //     break
  //   case 'online':
  //     console.log(`[当前人气]:${msg.count}`)
  //     break
  // }
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
