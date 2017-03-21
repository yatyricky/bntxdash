import React from 'react';
import {Flag} from '../Flag.js';
import ReactHighcharts from 'react-highcharts';

class RobotCoinsSum extends React.Component {

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

        xhr.open('POST', 'api.php');
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
        xhr.send(encodeURI("do=robot_coins_sum"));
        this.setState({flag: Flag.waiting});
    }

    renderTable() {
        const config = {categories: [], data: []};
        const entries = this.state.result.resp.map((item, index) => {
            config.categories.push(item.time);
            config.data.push(item.value);
            return (
                <tr key={index}>
                    <td>{item.time}</td>
                    <td>{item.value}</td>
                    <td>{item.played}</td>
                </tr>
            );
        });
        return (
            <table className="table table-striped">
                <thead>
                    <tr>
                        <th>时间</th>
                        <th>筹码总量</th>
                        <th>今日总局数</th>
                    </tr>
                </thead>
                <tbody>
                    {entries}
                </tbody>
            </table>
        );
    }

    renderResult(flag) {
        let ret;
        switch (flag) {
            case Flag.success:
                ret = (
                    <div>
                        
                        <div className="table-responsive">{this.renderTable()}</div>
                    </div>
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

    componentDidMount() {
        this.postData();
    }

    render() {
        return (
            <div>
                <h1 className="page-header">机器人最近金币存量</h1>
                <div>{this.renderResult(this.state.flag)}</div>
            </div>
        );
    }

}

export default RobotCoinsSum;