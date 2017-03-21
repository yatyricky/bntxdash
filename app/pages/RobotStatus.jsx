import React from 'react';

const Flag = {
    "nothing": 100,
    "waiting": 101,
    "success": 200,
    "failed": 300
}

class RobotTable extends React.Component {

    render() {
        const now = moment().valueOf();
        let sumCoins = 0;
        let sumGMCoins = 0;
        let online = 0;
        let uniqueTables = [];
        const entries = this.props.list.map((item, index) => 
            {
                sumCoins += parseInt(item.coins);
                sumGMCoins += parseInt(item.gm_add_coins);
                online += parseInt(item.table_id) == 0 ? 0 : 1;
                if (uniqueTables.indexOf(item.table_id) == -1) {
                    uniqueTables.push(item.table_id);
                }
                return (
                    <tr key={index}>
                        <td>{item.account_id}</td>
                        <td>{item.table_id}</td>
                        <td>{item.nick_name}</td>
                        <td>{item.coins}</td>
                        <td>{item.gm_add_coins}</td>
                        <td>{parseFloat(item.coins) - parseFloat(item.gm_add_coins) - 18888}</td>
                        <td>{item.weight}</td>
                        <td>{item.robot_type == 1 ? "Mimics" : "Follower"}</td>
                        <td>{(function (roboTime, curTime) {
                            // var botTimeStr = "(" + moment(roboTime).format() + ")";
                            let botTimeStr = "";
                            if (roboTime <= curTime) {
                                return botTimeStr + "Awake";
                            } else {
                                let dur = moment.duration(roboTime - curTime);
                                return botTimeStr + dur.hours() + ":" + dur.minutes() + ":" + dur.seconds();
                            }
                        })(Number(item.wake_time) * 1000, now)}</td>
                        <td>{item.config_id}</td>
                        <td>{item.exp_level}</td>
                        <td>{item.add_coin_times}</td>
                        <td>{item.change_time}</td>
                    </tr>
                );
            }
        );
        return (
            <div>
                <div className="row">
                    <span className="col-xs-3"><label>机器人总数：</label>{`${online} / ${this.props.list.length}`}</span>
                    <span className="col-xs-3"><label>总筹码：</label>{sumCoins}</span>
                    <span className="col-xs-3"><label>总赢取：</label>{sumCoins - sumGMCoins - 18888 * this.props.list.length}</span>
                    <span className="col-xs-3"><label>牌桌数：</label>{uniqueTables.length - 1}</span>
                </div>
                <div className="table-responsive">
                    <table className="table table-striped">
                        <thead>
                            <tr>
                                <th>account_id</th>
                                <th>table_id</th>
                                <th>nick_name</th>
                                <th>coins</th>
                                <th>gm_add_coins</th>
                                <th>won</th>
                                <th>weight</th>
                                <th>robot_type</th>
                                <th>wake_time</th>
                                <th>config_id</th>
                                <th>exp_level</th>
                                <th>add_coin_times</th>
                                <th>change_time</th>
                            </tr>
                        </thead>
                        <tbody>
                            {entries}
                        </tbody>
                    </table>
                </div>
            </div>
        );
    }

}

class RobotStatus extends React.Component {

    constructor() {
        super();
        this.postData = this.postData.bind(this);
        this.lastRequest = null;
        this.state = {
            "flag": Flag.nothing
        }
    }

    postData(params) {
        if (this.lastRequest != null) {
            this.lastRequest.abort();
        }

        const xhr = new XMLHttpRequest();
        this.lastRequest = xhr;

        xhr.open('POST', 'action.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=utf-8');
        xhr.onload = () => {
            this.lastRequest = null;
            if (xhr.status === 200) {
                const obj = JSON.parse(xhr.responseText);
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
        xhr.send(encodeURI(`do=get-robot-info&svr=${params}`));
        this.setState({flag: Flag.waiting});
    }

    componentWillUnmount() {
        if (this.lastRequest != null) {
            this.lastRequest.abort();
        }
    }

    renderResult(flag) {
        let ret;
        switch (flag) {
            case Flag.success:
                ret = (
                    <RobotTable list={this.state.result.robot_info} />
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

    render() {
        return (
            <div>
                <h1 className="page-header">选择一个服务器以查看</h1>
                <button className="btn" onClick={() => this.postData("beta")}>外网测试服</button>
                <button className="btn" onClick={() => this.postData("prod")}>中文正式服</button>
                <div>{this.renderResult(this.state.flag)}</div>
            </div>
        );
    }

}

export default RobotStatus;