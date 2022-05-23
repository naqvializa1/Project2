#!C:\Users\shaha\AppData\Local\Programs\Python\Python37\python.exe
#print("Content-type: text/html\n")

# Imports

import pandas as pd
import numpy as np
from pathlib import Path
import hvplot.pandas
import matplotlib.pyplot as plt
from pandas.tseries.offsets import DateOffset
from sklearn.preprocessing import StandardScaler
from sklearn.metrics import classification_report
from sklearn.metrics import accuracy_score, r2_score
from finta import TA
from sklearn.linear_model import LinearRegression, LogisticRegression
from sklearn.tree import DecisionTreeClassifier
from sklearn.svm import SVC
from sklearn.naive_bayes import GaussianNB
from sklearn.ensemble import RandomForestClassifier
import sys
import json
import base64



content = json.loads(base64.b64decode(sys.argv[1]))
dict2 = eval(content)

csvFile = dict2["csvFile"]



#OUT PUTS FOR FRONT END
finalJsonObject = {
  "ReportTitles": [],
  "dataFrame": [],
  "reports": [],
  "plots": []
}

#APPEND REPORT TITLE
def appendReportTitle(title):
    finalJsonObject["ReportTitles"].append(title)

#APPEND CLASSIFICATION
def appendClassReport(report):
    finalJsonObject["reports"].append(report)
   
    
#APPEND GRAPHS
def appendPlot(plot):
    finalJsonObject["plots"].append(plot)

 #APPEND DATAFRAME
def appendDf(value):
    finalJsonObject["dataFrame"].append(value)





# Read the file in dataframe
file_path = './resources/crude_ochl.csv'
technical_df = pd.read_csv(Path(file_path),index_col='Date',parse_dates=True,infer_datetime_format=True)



technical_df['actual_returns'] = technical_df['close'].pct_change()
technical_df = technical_df.dropna()


#APPEND DF
value = technical_df.head().to_html()
appendDf(value)
value = technical_df.tail().to_html()
appendDf(value)


sma_fast = 4
sma_slow = 100
ema = 50
volatility_window = 4



technical_df['sma_fast'] = TA.SMA(technical_df,sma_fast)
technical_df['sma_slow'] = TA.SMA(technical_df,sma_slow)
technical_df['ssma'] = TA.SSMA(technical_df)
technical_df['ema'] = TA.EMA(technical_df,ema)
technical_df['dema'] = TA.DEMA(technical_df)
technical_df['tema'] = TA.TEMA(technical_df)
technical_df['trima'] = TA.TRIMA(technical_df)
technical_df['volatility'] = technical_df['actual_returns'].rolling(window=volatility_window).std()

technical_df.dropna(inplace=True)


#APPEND DF
value = technical_df.head().to_html()
appendDf(value)
value = technical_df.tail().to_html()
appendDf(value)


#PLOT
plot = technical_df["volatility"].hvplot(title="volatility (Technical)")
hvplot.save(plot, './graphs/t.html')
fileName = "t.html"
appendPlot(fileName)


#PLOT
plot2 = technical_df["ema"].hvplot(title="ema (Technical)")
hvplot.save(plot2, './graphs/t2.html')
fileName = "t2.html"
appendPlot(fileName)

#PLOT
plot3 = technical_df["trima"].hvplot(title="trima (Technical)")
hvplot.save(plot3, './graphs/t3.html')
fileName = "t3.html"
appendPlot(fileName)

#PLOT
plot4 = technical_df["sma_fast"].hvplot(title="sma_fast (Technical)")
hvplot.save(plot4, './graphs/t4.html')
fileName = "t4.html"
appendPlot(fileName)


X = technical_df[['sma_fast','sma_slow','ssma','ema','dema','tema','trima','volatility']].shift().dropna().copy()




# Create Signal and populate them with 1 or -1
technical_df['signal'] = 0.0

# Create buy and sell signal
technical_df['signal'] = np.where(technical_df['actual_returns'] >=0, 1, -1)

y = technical_df['signal']


offset_years = 7
training_begin = X.index.min()
training_end = training_begin + DateOffset(years=offset_years)

X_train = X.loc[training_begin:training_end]
y_train = y.loc[training_begin:training_end]

test_begin = X.loc[training_end : ].index.min()
X_test = X.loc[test_begin : ]
y_test = y.loc[test_begin : ]





# Scale the features DataFrames

# Create a StandardScaler instance
scaler = StandardScaler()

# Apply the scaler model to fit the X-train data
X_scaler = scaler.fit(X_train)

# Transform the X_train and X_test DataFrames using the X_scaler
X_train_scaled = X_scaler.transform(X_train)
X_test_scaled = X_scaler.transform(X_test)



# Create models

# Logistic Regression model
LR_model = LogisticRegression(random_state=1)
LR_model.fit(X_train_scaled,y_train)
y_predict_test_LR = LR_model.predict(X_test_scaled)
appendReportTitle('Logistic Regression model')
appendClassReport(pd.DataFrame(classification_report(y_test,y_predict_test_LR, output_dict=True)).to_html())


# Decision Tree classifier model
DTC_model = DecisionTreeClassifier(random_state=1)
DTC_model.fit(X_train_scaled,y_train)
y_predict_test_DTC = DTC_model.predict(X_test_scaled)
appendReportTitle('Decision Tree Classifier')
appendClassReport(pd.DataFrame(classification_report(y_test,y_predict_test_DTC, output_dict=True)).to_html())



# SVM model
SVM_model = SVC(random_state=1)
SVM_model.fit(X_train_scaled,y_train)
y_predict_test_SVM = SVM_model.predict(X_test_scaled)
appendReportTitle('SVM Classifier')
appendClassReport(pd.DataFrame(classification_report(y_test,y_predict_test_SVM, output_dict=True)).to_html())



# GaussianNB model
GaussianNB_model = GaussianNB()
GaussianNB_model.fit(X_train_scaled,y_train)
y_predict_test_GaussianNB = GaussianNB_model.predict(X_test_scaled)
appendReportTitle('GaussianNB Classifier')
appendClassReport(pd.DataFrame(classification_report(y_test,y_predict_test_GaussianNB, output_dict=True)).to_html())



# RandomForestClassifier model
RandomForestClassifier_model = RandomForestClassifier()
RandomForestClassifier_model.fit(X_train_scaled,y_train)
y_predict_test_RandomForestClassifier = RandomForestClassifier_model.predict(X_test_scaled)
appendReportTitle('RandomForestClassifier Classifier')
appendClassReport(pd.DataFrame(classification_report(y_test,y_predict_test_RandomForestClassifier, output_dict=True)).to_html())







output_df = pd.DataFrame(index=X_test.index)
output_df.index.names = ['Date']
output_df['y'] = y_predict_test_LR
output_df.to_csv(Path('./resources/technical_output.csv'))





jsonStr = json.dumps(finalJsonObject)
print(jsonStr)


