
print("Content-type: text/html\n")

import pandas as pd
import numpy as np
from pathlib import Path
import hvplot
import hvplot.pandas  # noqa
import holoviews as hv
import matplotlib.pyplot as plt
from sklearn.preprocessing import StandardScaler
from sklearn.linear_model import LinearRegression, LogisticRegression
from pandas.tseries.offsets import DateOffset
from sklearn.metrics import classification_report