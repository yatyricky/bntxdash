import React from 'react';

class Card extends React.Component {

  render() {
    return (
      <div>
        <span>This is a Card!</span>
        <span>{this.props.name}</span>
      </div>
    );
  }

}

export default Card;