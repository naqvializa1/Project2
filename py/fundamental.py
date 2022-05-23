#!C:\Users\shaha\AppData\Local\Programs\Python\Python37\python.exe
#print("Content-type: text/html\n")

# Imports
# Imports

import pandas as pd
import numpy as np
from pathlib import Path
import hvplot.pandas
import matplotlib.pyplot as plt
from sklearn.preprocessing import StandardScaler
from sklearn.linear_model import LinearRegression, LogisticRegression
from pandas.tseries.offsets import DateOffset
from sklearn.metrics import classification_report
from sklearn.metrics import accuracy_score, r2_score
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
  "plots": [],
  "params": dict2["assetClass"]
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
file_path = f"./resources/{csvFile}"
fundamental_df = pd.read_csv(Path(file_path),index_col='date',parse_dates=True,infer_datetime_format=True)






#PLOT
plot = fundamental_df["spotPrice"].hvplot(title="Spot Price (Fundamental)")
hvplot.save(plot, './graphs/f1.html')
fileName = "f1.html"
appendPlot(fileName)


fundamental_df[
    ['spotPrice_pctchange',
     'frontFuture_pctchange',
     'aggregateOpenInterest_pctchange',
     'aggregateCallOpenInterest_pctchange',
     'aggregatePutOpenInterest_pctchange',
     'aggregateVolume_pctchange'
    ]] = fundamental_df[
    ['spotPrice',
     'frontMonthPrice',
     'aggregateOpenInterest',
     'aggregateCallOpenInterest',
     'aggregatePutOpenInterest',
     'aggregateVolume'
    ]].pct_change()



fundamental_df = fundamental_df.dropna()
fundamental_df = fundamental_df.drop(columns=['spotPrice','frontMonthPrice'])


#PLOT
plot2 = fundamental_df["frontFuture_pctchange"].hvplot(title="frontFuture_pctchange")
hvplot.save(plot2, './graphs/f2.html')
fileName = "f2.html"
appendPlot(fileName)











#APPEND DF
value = fundamental_df.head(5).to_html()
appendDf(value)
value = fundamental_df.tail(5).to_html()
appendDf(value)


fundamental_df['signal'] = np.where(fundamental_df['spotPrice_pctchange'] >=0,1,-1)
# y = fundamental_df['spotPrice_pctchange']
y = fundamental_df['signal']
fundamental_df.drop(columns=['signal'],inplace=True)
X = fundamental_df.shift().dropna()





#APPEND DF
value = fundamental_df.head(5).to_html()
appendDf(value)
value = fundamental_df.tail().to_html()
appendDf(value)







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

#REPORT OBJECT
logisticReport = classification_report(y_test,y_predict_test_LR, output_dict=True)
logisticReportHTML = pd.DataFrame(classification_report(y_test,y_predict_test_LR, output_dict=True)).to_html()
appendReportTitle('Logistic Regression model')
appendClassReport(logisticReportHTML)


# Decision Tree classifier model
DTC_model = DecisionTreeClassifier(random_state=1)
DTC_model.fit(X_train_scaled,y_train)
y_predict_test_DTC = DTC_model.predict(X_test_scaled)

#REPORT OBJECT
DecisionReport = classification_report(y_test,y_predict_test_DTC, output_dict=True)
DecisionReportHTML = pd.DataFrame(classification_report(y_test,y_predict_test_DTC, output_dict=True)).to_html()
appendReportTitle('Decision Tree Classifier')
appendClassReport(DecisionReportHTML)

# SVM model
SVM_model = SVC(random_state=1)
SVM_model.fit(X_train_scaled,y_train)
y_predict_test_SVM = SVM_model.predict(X_test_scaled)

#REPORT OBJECT
SVMReport = classification_report(y_test,y_predict_test_SVM, output_dict=True)
SVMReportHTML = pd.DataFrame(classification_report(y_test,y_predict_test_SVM, output_dict=True)).to_html()
appendReportTitle('SVM Classifier')
appendClassReport(SVMReportHTML)

# GaussianNB model
GaussianNB_model = GaussianNB()
GaussianNB_model.fit(X_train_scaled,y_train)
y_predict_test_GaussianNB = GaussianNB_model.predict(X_test_scaled)

#REPORT OBJECT
GaussianNBReport = classification_report(y_test,y_predict_test_GaussianNB, output_dict=True)
GaussianNBReportHTML = pd.DataFrame(classification_report(y_test,y_predict_test_GaussianNB, output_dict=True)).to_html()
appendReportTitle('GaussianNB Classifier')
appendClassReport(GaussianNBReportHTML)

# RandomForestClassifier model
RandomForestClassifier_model = RandomForestClassifier()
RandomForestClassifier_model.fit(X_train_scaled,y_train)
y_predict_test_RandomForestClassifier = RandomForestClassifier_model.predict(X_test_scaled)

#REPORT OBJECT
RandomForestReport = classification_report(y_test,y_predict_test_RandomForestClassifier, output_dict=True)
RandomForestReportHTML = pd.DataFrame(classification_report(y_test,y_predict_test_RandomForestClassifier, output_dict=True)).to_html()
appendReportTitle('RandomForestClassifier Classifier')
appendClassReport(RandomForestReportHTML)




output_df = pd.DataFrame(index=X_test.index)
output_df.index.names = ['Date']
output_df['y'] = y_predict_test_LR
output_df.to_csv(Path('./resources/fundamental_output.csv'))





jsonStr = json.dumps(finalJsonObject)
print(jsonStr)


