/**
 * Created by hantong on 18/3/15.
 */
const fs = require("fs");

fs.appendFile("./message.log", "data to append\n", (err) => {
  if (err)
    throw err;
  console.log("success");
});