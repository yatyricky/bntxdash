import React from 'react';
import {render} from 'react-dom';
import Card from './Card.jsx'

class App extends React.Component {
  render () {
    return <Card name="Vladoge Sorcerror" />;
  }
}

render(<App/>, document.getElementById('root'));