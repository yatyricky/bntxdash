import React from 'react';
import { IndexLink, Link } from "react-router";

class NavigationOptions extends React.Component {

    render() {
        const items = this.props.list.map((item, index) => 
            {
                if (item.to == "/") {
                    return (
                        <li key={index}>
                            <IndexLink to={item.to} activeClassName="active">{item.text}</IndexLink>
                        </li>
                    );
                } else {
                    return (
                        <li key={index}>
                            <Link to={item.to} activeClassName="active">{item.text}</Link>
                        </li>
                    );
                }
            }
        );
        return (
            <ul className="nav nav-sidebar">
                {items}
            </ul>
        );
    }

}

class NavigationCategory extends React.Component {

    render() {
        const categories = this.props.list.map((item, index) => 
            (
                <div key={index}>
                    <h3>{item.category}</h3>
                    <NavigationOptions list={item.options} />
                </div>
            )
        );
        return (
            <div className="col-sm-3 col-md-2 sidebar">
                {categories}
            </div>
        );
    }

}

class Navigation extends React.Component {

    render() {
        const menu = [
            {
                "category": "星冠德州",
                "options": [
                    {
                        "text": "总览",
                        "to": "/"
                    }
                ]
            },
            {
                "category": "机器人",
                "options": [
                    {
                        "text": "查询机器人状态",
                        "to": "robotStatus"
                    }, {
                        "text": "查询机器人筹码存量",
                        "to": "robotCoinsSum"
                    }, {
                        "text": "查询机器人配置",
                        "to": "queryRobotConfig"
                    }, {
                        "text": "机器人影响留存",
                        "to": "robotOnRetention"
                    }, {
                        "text": "机器人每日牌局表现",
                        "to": "robotPerformance"
                    }, {
                        "text": "玩家赢取机器人筹码",
                        "to": "playerWonRobots"
                    }
                ]
            }, {
                "category": "玩家",
                "options": [
                    {
                        "text": "当前在线玩家",
                        "to": "currentOnline"
                    }, {
                        "text": "修改玩家属性",
                        "to": "modPlayerStats"
                    }
                ]
            }, {
                "category": "系统",
                "options": [
                    {
                        "text": "操作记录",
                        "to": "serverLogs"
                    }
                ]
            }
        ];
        return (
            <NavigationCategory list={menu} />
        );
    }

}

export default Navigation;