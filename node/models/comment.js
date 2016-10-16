module.exports = function(mongoose) {
  
  var Schema = mongoose.Schema;
  
  var Comment = mongoose.model('Comment', {
    id: String,
    attachment: Schema.Types.Mixed,
    from: {
      name: String,
      id: String
    },
    created_time: Date,
    message: String,
    active: { type: Boolean, default: true }
  });

  return Comment;

}