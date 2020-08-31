const express = require('express');
const path = require('path');
const router = express.Router();


var app = express();
app.use(express.static('data'))
app.use(express.static('javascript'))
app.use('', express.static(path.join(__dirname, 'public')))

app.get('/expense', (req, res) => {
    res.sendFile(path.join(__dirname + '/public/expense.html'));

})

app.get('/calculator', (req, res) => {
    res.sendFile(path.join(__dirname + '/public/calculator.html'));
})
app.get('/todo', (req, res) => {
    res.sendFile(path.join(__dirname + '/public/todo.html'));
})



app.listen(3000, () => console.log(('port:3000')))