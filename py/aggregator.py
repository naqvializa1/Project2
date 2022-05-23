#!C:\Users\shaha\AppData\Local\Programs\Python\Python37\python.exe
#print("Content-type: text/html\n")


# Imports the required libraries
import numpy as np
import pandas as pd
from pathlib import Path
import warnings
warnings.filterwarnings('ignore')

# Imports the machine learning modules
from sklearn.model_selection import train_test_split
from imblearn.over_sampling import RandomOverSampler
from sklearn.preprocessing import StandardScaler
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import RandomForestClassifier
from sklearn import svm


# Imports the data reporting modules
from sklearn.metrics import balanced_accuracy_score
from sklearn.metrics import confusion_matrix
from sklearn.metrics import classification_report
import json


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






# Inputs the CSV Path for the three (3) antecedent models
# Load the Oil Returns Data into "csv_path_1"
csv_path_1 = Path("./resources/technical_output.csv")
csv_path_2 = Path("./resources/fundamental_output.csv")
csv_path_3 = Path("./resources/macro_output.csv")

#Loads the CSV files
model_one_output = pd.read_csv(csv_path_1, index_col="Date", parse_dates=True, infer_datetime_format=True)
model_two_output = pd.read_csv(csv_path_2, index_col="Date", parse_dates=True, infer_datetime_format=True)
model_three_output = pd.read_csv(csv_path_3, index_col="Date", parse_dates=True, infer_datetime_format=True)




model_one_output = model_one_output[~model_one_output.index.duplicated(keep='first')]
model_two_output = model_two_output[~model_two_output.index.duplicated(keep='first')]
model_three_output = model_three_output[~model_three_output.index.duplicated(keep='first')]



model_one_output = model_one_output["y"].astype(float)
model_two_output = model_two_output["y"].astype(float)
model_three_output = model_three_output["y"].astype(float)


#Concatenates all three into a single DataFrame (called "model_inputs")
model_inputs = pd.concat([model_one_output, model_two_output, model_three_output],  axis= "columns", join="inner")

#Renames columns to generic variables
column_names = ["x_1 linear", "x_2 linear", "x_3 linear"]
model_inputs.columns = column_names

#Creates three (3) additional inputs that squares the  original data
model_inputs["x_1 exponential"] = model_inputs["x_1 linear"] * model_inputs["x_1 linear"]
model_inputs["x_2 exponential"] = model_inputs["x_2 linear"] * model_inputs["x_2 linear"]
model_inputs["x_3 exponential"] = model_inputs["x_3 linear"] * model_inputs["x_3 linear"]

#Initializes the new Signal column
model_inputs['Signal'] = 0.0

#When Actual Returns ("x_1 linear") are greater than or equal to 0, generates signal to buy stock long
model_inputs.loc[(model_inputs["x_1 linear"] >= 1), 'Signal'] = 1

#When Actual Returns ("x_1 linear") are less than 1, generates signal to sell oil
model_inputs.loc[(model_inputs["x_1 linear"] < 1), 'Signal'] = -1

#Calculates the strategy returns and add them to the signals_df DataFrame
model_inputs['Strategy Returns'] = model_inputs['x_1 linear'] * model_inputs['Signal'].shift()

#Prints cleaned DataFrame
model_inputs = model_inputs.dropna()

#APPEND DF
value = model_inputs.head().to_html()
appendDf(value)
value = model_inputs.tail().to_html()
appendDf(value)


#Creates the target set selecting the Signal column and assiging it to y
y = model_inputs["Signal"]

#Creates the model inputs by dropping Signal & Strategy Returns columns and assigning it to X
X = model_inputs.drop(columns=["Signal", "Strategy Returns"])

#Splits the data using train_test_split and assigns a random_state of 1 to the function
X_train, X_test, y_train, y_test = train_test_split(X, y, random_state=1)

#Checks the y values to determine if oversampling is needed
y.value_counts()


#Instantiates the random oversampler model with a random_state parameter of 1
random_oversampler = RandomOverSampler(random_state=1)

#Fits the original training data to the random_oversampler model
X_resampled, y_resampled = random_oversampler.fit_resample(X_train, y_train)

#Checks the resamped y values
y_resampled.value_counts()



#Creates a StandardScaler instance
scaler = StandardScaler()

#Applys the scaler model to fit the X-train data
X_scaler = scaler.fit(X_resampled)

#Transforms the X_train and X_test DataFrames using the X_scaler
X_train_scaled = X_scaler.transform(X_resampled)
X_test_scaled = X_scaler.transform(X_test)



#Creates a LogisticRegression model and trains it on the X_train_scaled and y_resampled
regression_model = LogisticRegression()
regression_model.fit(X_train_scaled, y_resampled)

#Uses the model you trained to predict using X_test_scaled
y_pred = regression_model.predict(X_test_scaled)



#Prints out a classification report to evaluate performance
appendReportTitle('Logistic Regression model')
appendClassReport(pd.DataFrame(classification_report(y_test, y_pred, digits=4, output_dict=True)).to_html())





#Creates a RandomForestClassifier model and trains it on the X_train_scaled
forest_model = RandomForestClassifier(random_state=0)

#Uses the model you trained to predict using X_test_scaled
forest_model.fit(X_train_scaled, y_resampled)
y_pred = forest_model.predict(X_test_scaled)

#Creates a classification report to evaluate performance and saves it
appendReportTitle('RandomForestClassifier')
appendClassReport(pd.DataFrame(classification_report(y_test,y_pred, digits=4, output_dict=True)).to_html())



#From SVM, instantiate SVC classifier model instance
svm_model = svm.SVC()
 
#Fit the model to the data using the training data
svm_model = svm_model.fit(X_train_scaled, y_resampled)
 
#Uses the testing data to make the model predictions
svm_pred = svm_model.predict(X_test_scaled)

#Creates a classification report to evaluate performance and saves it
appendReportTitle('RandomForestClassifier')
appendClassReport(pd.DataFrame(classification_report(y_test, y_pred, digits=4, output_dict=True)).to_html())



jsonStr = json.dumps(finalJsonObject)
print(jsonStr)









