import React from 'react';

const Flag = {
    "nothing": 100,
    "waiting": 101,
    "success": 200,
    "failed": 300
}

class RobotPerformance extends React.Component {

    constructor() {
        super();
        this.postData = this.postData.bind(this);
        this.lastRequest = null;
        this.state = {
            "flag": Flag.nothing
        }
    }

    postData() {
        if (this.lastRequest != null) {
            this.lastRequest.abort();
        }

        const xhr = new XMLHttpRequest();
        this.lastRequest = xhr;

        xhr.open('POST', 'api/robotPerformance.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=utf-8');
        xhr.onload = () => {
            this.lastRequest = null;
            if (xhr.status === 200) {
                this.setState({
                    flag: Flag.success,
                    result: xhr.responseText
                });
            } else if (xhr.status !== 200) {
                this.setState({
                    flag: Flag.failed,
                    result: xhr.status
                });
            }
        };
        xhr.send(encodeURI(`date=${this.refs.inputDate.value}`));
        this.setState({flag: Flag.waiting});
    }

    renderTable() {
        const finalResult = (<textarea className="form-control" defaultValue={this.state.result} />);
        let obj = null;
        try {
            obj = JSON.parse(this.state.result);

            let actT="";
            let hands="";
            let bbalance="";

            const arrayLength = Object.keys(obj).length;
            const entries = Object.keys(obj).map((k, index) => {
                actT+=obj[k]['playedBots']+(arrayLength === index + 1 ? "" : ",");
                hands+=obj[k]['playedHands']+(arrayLength === index + 1 ? "" : ",");
                bbalance+=obj[k]['balance']+(arrayLength === index + 1 ? "" : ",");
                return (
                    <tr key={index}>
                        <td className="text-right">{k}</td>
                        <td className="text-right">{obj[k]['playedBots']}</td>
                        <td className="text-right">{obj[k]['playedHands']}</td>
                        <td className="text-right">{Number(obj[k]['balance']).toLocaleString()}</td>
                    </tr>
                );
            });
            return (
                <div>
                    <div className="table-responsive">
                        <table className="table table-striped">
                            <thead>
                                <tr>
                                    <th className="text-right">类型</th>
                                    <th className="text-right">出勤数</th>
                                    <th className="text-right">牌局数</th>
                                    <th className="text-right">盈亏平衡</th>
                                </tr>
                            </thead>
                            <tbody>
                                {entries}
                            </tbody>
                        </table>
                    </div>
                    <textarea className="form-control" defaultValue={actT+hands+bbalance} />
                    {finalResult}
                </div>
            );
        } catch(e) {
            return finalResult;
        }
    }

    renderResult(flag) {
        let ret;
        switch (flag) {
            case Flag.success:
                ret = (
                    <div>{this.renderTable()}</div>
                );
                break;
            case Flag.failed:
                ret = (<div>{`Request Failed: ${this.state.result}`}</div>);
                break;
            case Flag.waiting:
                ret = (<div className="loader" />);
                break;
            default:
                ret = (<div />);
        }
        return ret;
    }

    componentWillUnmount() {
        if (this.lastRequest != null) {
            this.lastRequest.abort();
        }
    }

    render() {
        return (
            <div>
                <h1 className="page-header">机器人每日牌局表现</h1>
                <form>
                    <span>选择日期：</span>
                    <input type="date" ref="inputDate" className="input-sm" onChange={this.postData} />
                </form>
                <div>{this.renderResult(this.state.flag)}</div>
            </div>
        );
    }

}

export default RobotPerformance;