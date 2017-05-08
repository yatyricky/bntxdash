## BaiNa TeXas DASHboard

服务于百纳公司的星冠德州扑克项目。由于内测阶段数据格式变更较快，对快速分析数据有较高要求，因此没有使用公司内部的数据系统。

## 总览

用于展示常用数据表，尚未开发。

## 查询机器人状态

![robotStatus](https://raw.githubusercontent.com/yatyricky/bntxdash/master/imgs/readme/robotStatus.jpg)

顾名思义，查询目前游戏中的机器人状态。

## 查询机器人配置

截止到5月2日，不再更新

## 机器人筹码变动

![robotCoinsSum](https://raw.githubusercontent.com/yatyricky/bntxdash/master/imgs/readme/robotCoinsSum.jpg)

* 筹码总量：每天23:59分对GS上的所有机器人筹码进行一次求和
* 毛盈利：未抽水前赢取筹码
* 误差：除筹码总量外，其他数据由日志计算得出，因此有1分钟的误差
* 服务器需要大约1秒/天的时间来计算，选择天数越多计算时间越长
* 如果超过预期时间，请刷新页面

## 机器人影响留存

将玩家分为5类，计算与机器人进行不同程度的交互之后的次日留存对比。

## 机器人每日牌局表现

计算机器人的每日牌局数据。

### 机器人出勤数量

![robotPerformance1](https://raw.githubusercontent.com/yatyricky/bntxdash/master/imgs/readme/robotPerformance1.jpg)

### 机器人人均牌局数

![robotPerformance2](https://raw.githubusercontent.com/yatyricky/bntxdash/master/imgs/readme/robotPerformance2.jpg)

### 机器人手均盈亏BB数

![robotPerformance3](https://raw.githubusercontent.com/yatyricky/bntxdash/master/imgs/readme/robotPerformance3.jpg)

## 留存率

![playerRetention](https://raw.githubusercontent.com/yatyricky/bntxdash/master/imgs/readme/playerRetention.jpg)

内部数据后台的留存数据后来出现不准确的情况，因此制作留存率分析页面。

## 当前在线玩家

顾名思义。

## 修改玩家属性

顾名思义。

## 玩家筹码变动

类似机器人筹码变动分析。

## 玩家赢取机器人筹码

日志修复后未升级该页面，需要更新算法。

## 操作记录

早期GS提供的http接口不是很稳定，因此需要记录从bntxdash发出的请求，用于GS定位问题。