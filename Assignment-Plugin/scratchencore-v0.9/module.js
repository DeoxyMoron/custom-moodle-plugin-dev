// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * JavaScript library for the @@newmodule@@ module.
 *
 * @package    mod
 * @subpackage @@newmodule@@
 * @copyright  COPYRIGHTNOTICE
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.mod_scratchencore = M.mod_scratchencore || {};

M.mod_scratchencore.helper = {
	gY: null,


	 /**
     * @param Y the YUI object   /// ????
     * @param opts an array of options
     */
    init: function(Y,opts) {
    	M.mod_scratchencore.helper.gY = Y;
    	console.log(opts['someinstancesetting']);
    },

		test: function(Y,opts) {
			var data = opts['json'];
			console.log("wooooooo");
		},

		Sprite: function(object){
		  this.name = object;
		  this.scripts = [];
		  this.parent = '';
		  this.children = [];
		  this.instructions = [];
			this.foobar = "boofar";
			console.log("hehe")
		},

		make_sprite: function(name){
		  sprite = new M.mod_scratchencore.helper.Sprite(name);
			console.log("eef")
		  return sprite;
		},

		checkCode: function(opcode){
		  var opcode_target = "whenGreenFlag";
		  return opcode == opcode_target;
		},

		main: function(Y,opts){

		  // Read JSON file
		  var data = JSON.parse(opts['json']);


			//console.log(data);
			//console.log(data);
		  // Process parent sprite (background, stage, etc)
		  var parent = M.mod_scratchencore.helper.make_sprite(data["objName"])

		  if ('scripts' in data){

			//for (var key in object) {
		    parent.scripts = data["scripts"];
		  };


		  for (var i = 0; i < parent.scripts.length; i++){ //hardcoded
		      parent.instructions.push(parent.scripts[i][2])
		    };

		  //  Iterate over children sprites if there are children
		 // if ('children' in data){
  		if ('children' in data){
		    var a = data["children"];

				a.forEach(function(element){
						console.log(element);
				});

		    var iterator = a.entries();



		    for (let e of iterator) {
		      var index = e[0];
		      var item = e[1];

		      child = M.mod_scratchencore.helper.make_sprite(item["objName"])
		      if ('scripts' in item){
		          child.scripts = item["scripts"]
		        };

		      // pull out the actual instructions, which is the 3rd index in the script
		      ////for (itr in range(0,child.scripts.length)){
		      for (var i = 0; i < child.scripts.length; i++){
		          child.instructions.push(child.scripts[i][2])
		        };

		      child.parent = parent;
		      parent.children.push(child);
		    };

		    for (var i = 0; i < parent.children.length; i++){

		    };

		  };

		  // Example: Count the number of whenGreenFlag blocks
		  var num_greenFlag = 0;
		  var opcode_target = "whenGreenFlag";

		  var array0 = parent.instructions;
		  for (a in array0){
		    //console.log(array1[a][1])
		    for (b in array0[a]){
		      //console.log(array1[a][b])

		      var blocks = array0[a][b];
		      //onsole.log((blocks.every(checkCode)))
		      if (blocks.every(M.mod_scratchencore.helper.checkCode)){
		        num_greenFlag += 1;
		      }
		      //if (opcode_target in array1[a][b]){
		      //  console.log("PIKA")
		      //}
		    }
		  }

		  for (q in parent.children){
		    //console.log(q)
		    var array1 = parent.children[q].instructions;
		    for (a in array1){
		      //console.log(array1[a][1])
		      for (b in array1[a]){
		        //console.log(array1[a][b])

		        var blocks = array1[a][b];
		        //onsole.log((blocks.every(checkCode)))
		        if (blocks.every(M.mod_scratchencore.helper.checkCode)){
		          num_greenFlag += 1;
		        }
		        //if (opcode_target in array1[a][b]){
		        //  console.log("PIKA")
		        //}
		      }
		    }
		  }

		  console.log("Number of Green Flag blocks:");
		  console.log(num_greenFlag);

			console.log(Y.one("#foobar").setHTML(num_greenFlag));
			return num_greenFlag;
		}
};
