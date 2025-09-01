import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, Button, ActivityIndicator, ScrollView } from 'react-native';
import { tryMultipleBaseUrls, API } from '../src/api';
import { useNavigation } from '@react-navigation/native';

/**
 * Test component to verify API connections
 */
const ConnectionTestScreen = () => {
  const navigation = useNavigation();
  const [testResults, setTestResults] = useState<{endpoint: string, success: boolean, message: string}[]>([]);
  const [testing, setTesting] = useState(false);
  const [autoRunComplete, setAutoRunComplete] = useState(false);
  
  // Auto-run the tests when the screen loads
  useEffect(() => {
    // Small delay to let the screen render first
    const timer = setTimeout(() => {
      if (!autoRunComplete) {
        runTests();
        setAutoRunComplete(true);
      }
    }, 500);
    
    return () => clearTimeout(timer);
  }, []);

  const runTests = async () => {
    setTesting(true);
    setTestResults([]);
    
    // List of endpoints to test
    const endpoints = [
      '/clinics',
      '/clinics/1/availability/summary',
      '/clinic/1/availability/summary', // Try alternative endpoint format
      '/clinics/1/availability/slots/2025-08-26',
      '/clinics/1/appointments/booked-slots/2025-08-26'
    ];
    
    for (const endpoint of endpoints) {
      try {
        setTestResults(prev => [...prev, {
          endpoint,
          success: false,
          message: 'Testing...'
        }]);
        
        console.log(`Testing endpoint: ${endpoint}`);
        const response = await tryMultipleBaseUrls(endpoint);
        
        setTestResults(prev => prev.map(item => 
          item.endpoint === endpoint 
            ? { 
                endpoint, 
                success: true, 
                message: `Success! Worked with URL: ${response.config.baseURL}`
              }
            : item
        ));
      } catch (error: any) {
        setTestResults(prev => prev.map(item => 
          item.endpoint === endpoint 
            ? { 
                endpoint, 
                success: false, 
                message: `Error: ${error?.message || 'Unknown error'}`
              }
            : item
        ));
      }
    }
    
    setTesting(false);
  };

  // Count successful tests
  const successCount = testResults.filter(r => r.success).length;
  const totalCount = testResults.length;
  const allSuccess = totalCount > 0 && successCount === totalCount;
  
  return (
    <View style={styles.container}>
      <Text style={styles.title}>API Connection Test</Text>
      <Text style={styles.subtitle}>This utility tests connections to different API endpoints</Text>
      
      {testResults.length > 0 && (
        <View style={[
          styles.summaryBox,
          {backgroundColor: allSuccess ? '#e6ffe6' : '#fff9e6'}
        ]}>
          <Text style={styles.summaryText}>
            {successCount} of {totalCount} tests successful
            {allSuccess ? ' - All endpoints working! ✅' : ' - Some endpoints failed ⚠️'}
          </Text>
          {!allSuccess && (
            <Text style={styles.fixText}>
              Don't worry! The app will still work using fallback methods and alternate endpoints.
            </Text>
          )}
        </View>
      )}
      
      <Button 
        title={testing ? "Testing..." : "Run Connection Tests"}
        onPress={runTests}
        disabled={testing}
        color="#4A6FA5"
      />
      
      {testing && (
        <ActivityIndicator size="large" color="#4A6FA5" style={styles.loader} />
      )}
      
      <ScrollView style={styles.resultsContainer}>
        {testResults.map((result, index) => (
          <View 
            key={index} 
            style={[
              styles.resultItem, 
              { backgroundColor: result.success ? '#e6ffe6' : '#ffe6e6' }
            ]}
          >
            <Text style={styles.endpointText}>{result.endpoint}</Text>
            <Text 
              style={[
                styles.resultText, 
                { color: result.success ? 'green' : 'red' }
              ]}
            >
              {result.message}
            </Text>
          </View>
        ))}
      </ScrollView>
      
      <View style={styles.footerContainer}>
        <Text style={styles.noticeText}>
          Found working server: {API.defaults.baseURL || 'Not yet determined'}
        </Text>
        <Button 
          title="Continue to App" 
          onPress={() => navigation.navigate('Login' as never)}
          color="#28a745"
        />
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 20,
    backgroundColor: '#f5f5f5',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  subtitle: {
    fontSize: 16,
    marginBottom: 20,
    color: '#666',
  },
  loader: {
    marginVertical: 20,
  },
  resultsContainer: {
    marginTop: 20,
    flex: 1,
  },
  resultItem: {
    padding: 15,
    borderRadius: 8,
    marginBottom: 10,
  },
  endpointText: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 5,
  },
  resultText: {
    fontSize: 14,
  },
  summaryBox: {
    padding: 15,
    borderRadius: 8,
    marginBottom: 20,
    borderWidth: 1,
    borderColor: '#ddd',
  },
  summaryText: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 5,
  },
  fixText: {
    fontSize: 14,
    color: '#666',
    fontStyle: 'italic',
  },
  footerContainer: {
    marginTop: 20,
    paddingTop: 15,
    borderTopWidth: 1,
    borderTopColor: '#ddd',
  },
  noticeText: {
    fontSize: 12,
    color: '#666',
    marginBottom: 10,
    textAlign: 'center',
  }
});

export default ConnectionTestScreen;
