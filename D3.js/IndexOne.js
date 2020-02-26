d3.select('h1').style('color', 'red').attr('class', 'heading').text('Updated h1 tag');
/*
d3.select('body').append('p').text('Hello World.');
d3.select('body').append('p').text('This is so crash!!!');
d3.selectAll('p').style('color', 'darkblue');
*/
var dataset = [1, 2, 3, 4, 5, 6];
var i;

//d3.select('body').selectAll('p').data(dataset).enter().append('p').text('D3.JS is totally tubular!');

d3.select('body').selectAll('p').data(dataset).enter().append('p').text(function(d)
    {
        return d;
    });