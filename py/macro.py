#!C:\Users\shaha\AppData\Local\Programs\Python\Python37\python.exe
#print("Content-type: text/html\n")

# Imports the required library
from tabnanny import verbose
import pandas as pd
from pathlib import Path
import tensorflow as tf
# Imports the machine learning modules
from tensorflow.keras.layers import Dense
from tensorflow.keras.models import Sequential
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler,OneHotEncoder
#imports the data reporting moules
from sklearn.decomposition import PCA
from sklearn.metrics import balanced_accuracy_score
from sklearn.metrics import confusion_matrix
from sklearn.metrics import classification_report
import numpy as np
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
  "dataFrameOutPut": [],
  "reports": [],
  "plots": [],
  "modelLossAccuracy": ""
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


#APPEND DATAFRAME
def appendDfOutPut(value):
    finalJsonObject["dataFrameOutPut"].append(value)


df = pd.read_csv(Path("./resources/Economic data_0.csv"))
df=df.rename(columns={'Row Labels':'Date'})

df=df.fillna(method="ffill")
df=df.fillna(0)





# -1, 0, 1
dummy_values = [-1, 1, 0]
#random.choice(dummy_values)
df["trend"] = np.random.choice(dummy_values, len(df))


y=df["trend"]
df=df.set_index(['Date'])
X=df.drop(columns='Grand Total')


#APPEND DF
value = df.head().to_html()
appendDf(value)
value = df.tail().to_html()
appendDf(value)





# Splits the data using train_test_split and assigns a random_state of 1 to the function
X_train, X_test, y_train, y_test = train_test_split(X, y, random_state=1)


# Creates a StandardScaler instance
scaler = StandardScaler()

# Applys the scaler model to fit the X-train data
X_scaler = scaler.fit(X_train)

# Transforms the X_train and X_test DataFrames using the X_scaler
X_train_scaled = X_scaler.transform(X_train)
X_test_scaled = X_scaler.transform(X_test)


# Define the the number of inputs (features) to the model
number_input_features = X.shape[1]

# Review the number of features
number_input_features


# Define the number of neurons in the output layer
number_output_neurons = 1


# Define the number of hidden nodes for the first hidden layer
hidden_nodes_layer1 = 20

# Review the number hidden nodes in the first layer
hidden_nodes_layer1



# Define the number of hidden nodes for the second hidden layer
hidden_nodes_layer2 =  20

# Review the number hidden nodes in the second layer
hidden_nodes_layer2



# Create the Sequential model instance
nn = Sequential()

# Add the first hidden layer
nn.add(
    Dense(units=hidden_nodes_layer1, input_dim=number_input_features, activation="relu")
)


# Add the second hidden layer
nn.add(Dense(units=hidden_nodes_layer2, activation="relu"))

# Add the output layer to the model specifying the number of output neurons and activation function
nn.add(Dense(units=1, activation="linear"))




# Compile the Sequential model
nn.compile(loss="mean_squared_error", optimizer="adam", metrics=["accuracy"])





# Fit the model using 50 epochs and the training data
model_1 = nn.fit(X, y, epochs=5, verbose = 0)




# Evaluate the model loss and accuracy metrics using the evaluate method and the test data
model_loss, model_accuracy = nn.evaluate(X_test_scaled , y_test, verbose=0)

# Display the model loss and accuracy results
finalJsonObject["modelLossAccuracy"] = f"Loss: {model_loss}<br>Accuracy: {model_accuracy}"



y_predictions=nn.predict(X_test_scaled)



output_df = pd.DataFrame(index=X_test.index)
output_df.index.names = ['Date']
output_df['y'] = y_predictions
output_df.to_csv(Path('./resources/macro_output.csv'))


value = output_df.head().to_html()
appendDfOutPut(value)
value = output_df.tail().to_html()
appendDfOutPut(value)




jsonStr = json.dumps(finalJsonObject)
print(jsonStr)






