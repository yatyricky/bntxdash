import React from 'react';

const Flag = {
    "nothing": 100,
    "waiting": 101,
    "success": 200,
    "failed": 300
}

class RobotOnRetention extends React.Component {

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

        xhr.open('POST', 'api/action.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=utf-8');
        xhr.onload = () => {
            this.lastRequest = null;
            if (xhr.status === 200) {
                const obj = xhr.responseText;
                this.setState({
                    flag: Flag.success,
                    result: obj
                });
            } else if (xhr.status !== 200) {
                this.setState({
                    flag: Flag.failed,
                    result: xhr.status
                });
            }
        };
        xhr.send(encodeURI(`do=robot-analysis-retention&value=${this.refs.inputDate.value}`));
        this.setState({flag: Flag.waiting});
    }

    calcPercentage(a, b) {
        if (b == 0) {
            return "0.00%";
        } else {
            const v = parseFloat(a) / parseFloat(b) * 100;
            return v.toFixed(2) +"%";
        }
    }

    renderTable() {
        const original = this.state.result.replace(/['"\\rn]+/g, '').trim();
        const datas = original.split(",")
        const finalResult = (<textarea className="form-control" defaultValue={original} />);
        if (datas.length < 10) {
            return finalResult;
        } else {
            return (
                <div>
                    <div className="table-responsive">
                        <table className="table table-striped">
                            <thead>
                                <tr>
                                    <th>分类</th>
                                    <th>新玩家数量</th>
                                    <th>次日留存数</th>
                                    <th>次日留存率</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>A-没有与机器人交互</td>
                                    <td>{parseInt(datas[0])}</td>
                                    <td>{parseInt(datas[5])}</td>
                                    <td>{this.calcPercentage(datas[5], datas[0])}</td>
                                </tr>
                                <tr>
                                    <td>B-没有与机器人交互，参与牌局（暂无统计）</td>
                                    <td>{parseInt(datas[1])}</td>
                                    <td>{parseInt(datas[6])}</td>
                                    <td>{this.calcPercentage(datas[6], datas[1])}</td>
                                </tr>
                                <tr>
                                    <td>C-与机器人交互，赢钱</td>
                                    <td>{parseInt(datas[2])}</td>
                                    <td>{parseInt(datas[7])}</td>
                                    <td>{this.calcPercentage(datas[7], datas[2])}</td>
                                </tr>
                                <tr>
                                    <td>D-与机器人交互，输钱小于18888</td>
                                    <td>{parseInt(datas[3])}</td>
                                    <td>{parseInt(datas[8])}</td>
                                    <td>{this.calcPercentage(datas[8], datas[3])}</td>
                                </tr>
                                <tr>
                                    <td>E-与机器人交互，输钱大于等于18888</td>
                                    <td>{parseInt(datas[4])}</td>
                                    <td>{parseInt(datas[9])}</td>
                                    <td>{this.calcPercentage(datas[9], datas[4])}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <span>玩家-机器人交互率：</span>
                        <span>{this.calcPercentage(parseFloat(datas[2])+parseFloat(datas[3])+parseFloat(datas[4]), parseFloat(datas[0])+parseFloat(datas[1])+parseFloat(datas[2])+parseFloat(datas[3])+parseFloat(datas[4]))}</span>
                    </div>
                    {finalResult}
                </div>
            );
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
                <h1 className="page-header">机器人对新玩家的留存影响</h1>
                <form>
                    <span>选择日期：</span>
                    <input type="date" ref="inputDate" className="input-sm" onChange={this.postData} />
                </form>
                <div>{this.renderResult(this.state.flag)}</div>
            </div>
        );
    }

}

export default RobotOnRetention;