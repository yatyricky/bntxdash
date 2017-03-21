import React from 'react';
import {Flag} from '../Flag.js';
import ReactHighcharts from 'react-highcharts';

class RobotCoinsSum extends React.Component {

    constructor() {
        super();
        this.postData = this.postData.bind(this);
        this.lastRequest = null;
        this.config = {categories: [], data: []};
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
        const entries = this.state.result.resp.map((item, index) => {
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
                for (let i = 0; i < this.state.result.resp.length; i++) {
                    this.config.categories.push(this.state.result.resp[i].time.split(" ")[0]);
                    this.config.data.push(parseInt(this.state.result.resp[i].value));
                }
                const highConfig = {
                    title: {
                        text: '每日筹码存量'
                    },
                    xAxis: {
                        categories: this.config.categories
                    },
                    yAxis: {
                        title: {
                            text: '总量'
                        }
                    },
                    series: [{
                        name: '总筹码',
                        data: this.config.data
                    }]

                };
                ret = (
                    <div>
                        <ReactHighcharts config = {highConfig} />
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

    componentWillUnmount() {
        if (this.lastRequest != null) {
            this.lastRequest.abort();
        }
    }

    render() {
        return (
            <div>
                <h1 className="page-header">机器人最近筹码存量</h1>
                <div>{this.renderResult(this.state.flag)}</div>
            </div>
        );
    }

}

export default RobotCoinsSum;