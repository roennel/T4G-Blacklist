Array.prototype.implode = function(spl, exclude)
{
  var result = '';
  
  for(i = 0, c = this.length;i < c;i++)
  {
    if(!this[i]) continue;
    
    if(this[i] != exclude)
    {
      result+= this[i];
    
      if(i < (c-1))
      {
        result+= spl;
      }
    }
  }
  
  return result;
};

Element.prototype.selectByValue = function(value)
{
  var opt = this;
  
  if(opt.getChildren('optgroup').length > 0)
  {
    opt.getChildren().each(function(item)
    {
      item.selectByValue(value);
    });
    
    return;
  }
  
  opt.getChildren('option').each(function(item)
  {
    if(item.get('value') == value)
    {
      item.set('selected', 'selected');
    }
  });
};