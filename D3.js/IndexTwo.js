
var dataset = [20, 40, 60, 40, 210, 341, 120];
//var dataset = [1, 2, 3, 4, 5, 6];

var svgWidth = 500;
var svgHeight = 375;
var barPadding = 5;
var barWidth = (svgWidth / dataset.length);

var svg = d3.selectAll('svg')
    .attr("width", svgWidth)
    .attr("height", svgHeight);

var xScale = d3.scaleLinear()
    .domain([0, d3.max(dataset)])
    .range([0, svgWidth])

var yScale = d3.scaleLinear()
    .domain([0, d3.max(dataset)])
    .range([svgHeight, 0])

var xAxis = d3.axisBottom().scale(xScale);

var yAxis = d3.axisLeft().scale(yScale);

svg.append("g")
    .attr("transform", "translate(50, 10)")
    .call(yAxis);

var xAxisTranslate = svgHeight - 20;

svg.append("g")
    .attr("transform", "translate(50, " + xAxisTranslate + ")")
    .call(xAxis);

d3.select("body").transition().style("background-color", "lightgreen");


/*
var barChart = svg.selectAll("rect")
    .data(dataset)
    .enter()
    .append("rect")
    .attr("y", function(d)
    {
        return svgHeight - yScale(d);
    })
    .attr("height", function(d)
    {
        return yScale(d);
    })
    .attr("width", barWidth - barPadding)
    .attr("transform", function(d, i)
    {
        var translate = [barWidth*i, 0];
        return "translate(" + translate + ")";
    });




var text = svg.selectAll("text")
    .data(dataset)
    .enter()
    .append("text")
    .text(function(d)
    {
        return d;
    })
    .attr("y", function(d, i)
    {
        return svgHeight - yScale(d) - 2;
    })
    .attr("x", function(d, i)
    {
        return barWidth * i;
        //return xScale(d) * i;
    })
    .attr("fill", "#ff0000")
    .attr("font-weight", "bold");

*/