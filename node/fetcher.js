var graph = require('fbgraph');
var Q = require("q");
var async = require('async');
var _ = require('underscore');
var mongoose = require('mongoose');
var Comment = require('./models/comment.js')(mongoose);

mongoose.connect('mongodb://localhost/fbfetch');

// token start: 18/8/2016
var access_token = 'EAACr0TDCNmQBAPsZAQlbyJyJZAowGBi7ftuZCN1hqbiL0GY7zZCD9Vgy2Hq5YU6N52PHSW5OFgcqy2qgr9nDUEjLkACsZCh3RanPvSrGJZCuTdckB67994aTkYLv6BZBATXeOWoGDbkUPHfJHPKCH5Dkz2vEDudQdcZD';
var post_id = '1349322428431102';

// graph.extendAccessToken({
//     "client_id": '188914954548836'
//   , "client_secret": 'dc8d276bcae68cb0486681b39684fc3a'
// }, function (err, facebookRes) {
//    console.log(facebookRes);
// });

graph.setAccessToken(access_token);

var options = {
    timeout:  30000
  , pool:     { maxSockets:  Infinity }
  , headers:  { connection:  "keep-alive" }
};

function save(comments) {
  var deferred = Q.defer();

  async.each(comments, function(comment, callback) {

    Comment.findOneAndUpdate({id: comment.id}, comment, {upsert:true}, function(err, doc){
      if (err) return callback(err);
      else callback();
    });

  }, function(err) {
    // all done
    if(err) {
      deferred.reject(err);
    } else {
      deferred.resolve();
    }
  });

  return deferred.promise;
}

function getComments() {
  var deferred = Q.defer();
  var after = null;
  var retry = 0;
  var original_url = post_id + '/comments?order=reverse_chronological&filter=toplevel&fields=id,attachment,message,from,created_time';

  async.whilst(
    function() { 
      return after != -1 && retry < 5;
    },
    function(callback) {

      if(after) {
        url = original_url + '&after=' + after;
      } else {
        url = original_url;
      }

      graph
        .setOptions(options)
        .get(url, function(err, res) {

          console.log(res);
          
          // concat response
          if(res && res.data && res.data.length) {
            save(res.data)
            .then(function(){
              retry = 0;

              // set next cursor
              if(res && res.paging && res.paging.cursors && res.paging.cursors.after) {
                after = res.paging.cursors.after;
              } else {
                after = -1; // get out of loop
              }

              // done
              // retry = 5;
              callback();
            })
            .catch(function(error) {
              callback(error);
            });
          } else {
            // something's wrong - try again?
            retry = retry + 1;
            console.log('error: retry  #' + retry + ' ' + url);
            callback();
          }


        });
    },
    function (err, n) {
        // all done - goodbye
        if(err) deferred.reject(err);
        else deferred.resolve();
    }
  );


  return deferred.promise;
}

function init() {
  getComments()
  .then(function(comments){
    console.log('all done!');
  })
  .catch(function(error) {
    console.log(error);
  })
  .fin(function () {
    mongoose.connection.close();
  });
}

init();
