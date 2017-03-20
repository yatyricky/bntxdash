import React from 'react';
import {render} from 'react-dom';
import { Router, Route, IndexRoute, hashHistory } from "react-router";

import Layout from './Layout.jsx';
import Welcome from './pages/Welcome.jsx';

import RobotStatus from './pages/RobotStatus.jsx';
import QueryRobotConfig from './pages/QueryRobotConfig.jsx';
import RobotOnRetention from './pages/RobotOnRetention.jsx';
import RobotPerformance from './pages/RobotPerformance.jsx';
import PlayerWonRobots from './pages/PlayerWonRobots.jsx';

import CurrentOnline from './pages/CurrentOnline.jsx';
import ModPlayerStats from './pages/ModPlayerStats.jsx';

import ServerLogs from './pages/ServerLogs.jsx';

class Main extends React.Component {
    render () {
        return (
            <Router history={hashHistory}>
                <Route path="/" component={Layout}>
                    <IndexRoute component={Welcome}></IndexRoute>
                    <Route path="robotStatus" name="robotStatus" component={RobotStatus}></Route>
                    <Route path="queryRobotConfig" name="queryRobotConfig" component={QueryRobotConfig}></Route>
                    <Route path="robotOnRetention" name="robotOnRetention" component={RobotOnRetention}></Route>
                    <Route path="robotPerformance" name="robotPerformance" component={RobotPerformance}></Route>
                    <Route path="playerWonRobots" name="playerWonRobots" component={PlayerWonRobots}></Route>

                    <Route path="currentOnline" name="currentOnline" component={CurrentOnline}></Route>
                    <Route path="modPlayerStats" name="modPlayerStats" component={ModPlayerStats}></Route>

                    <Route path="serverLogs" name="serverLogs" component={ServerLogs}></Route>
                </Route>
            </Router>
        );
    }
}

render(<Main />, document.getElementById('root'));