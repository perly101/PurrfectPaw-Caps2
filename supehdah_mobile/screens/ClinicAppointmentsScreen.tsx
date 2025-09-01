import React from 'react';
import { View, Text, StyleSheet, ScrollView, TextInput, TouchableOpacity, ActivityIndicator, Modal, FlatList, RefreshControl, Platform, Alert } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Ionicons } from '@expo/vector-icons';
import { API } from '../src/api';
import DateTimePicker from '@react-native-community/datetimepicker';
import { format } from 'date-fns';

const PINK = '#FFC1CC';
const PURPLE = '#B39DDB';
const WHITE = '#FFFFFF';
const DARK = '#333';
const LIGHT = '#F8F6FF';

// Backend shape
type ClinicField = {
  id: number;
  label: string;
  type: 'text' | 'textarea' | 'select' | 'checkbox' | 'radio' | 'date' | 'time' | 'number';
  options?: string[] | null;
  required?: boolean;
};

// Time slot interface to match the calendar selection
type TimeSlot = {
  start: string;
  end: string;
  display_time: string;
};

// Component props to accept calendar selection
type ClinicAppointmentsScreenProps = {
  route?: {
    params?: {
      clinicId?: number;
      date?: string;      // Calendar selected date in YYYY-MM-DD format
      timeSlot?: TimeSlot; // Selected time slot from calendar
    };
  };
  navigation?: any;
};

type OptionModalState = {
  visible: boolean;
  fieldId: number | null;
  label: string;
  multiple: boolean;
  options: string[];
  selected: string[]; // keep as array; for single we use length 0/1
};

type DateTimePickerState = {
  visible: boolean;
  fieldId: number | null;
  mode: 'date' | 'time';
  value: Date;
};

export default function ClinicAppointmentsScreen({ route, navigation }: ClinicAppointmentsScreenProps) {
  // Create ref for scrolling
  const scrollViewRef = React.useRef<ScrollView>(null);
  
  // Get parameters from route if available (from calendar screen)
  const routeParams = route?.params || {};
  const calendarDate = routeParams.date;
  const calendarTimeSlot = routeParams.timeSlot;
  const routeClinicId = routeParams.clinicId;
  
  const [clinicId, setClinicId] = React.useState<number | null>(routeClinicId || null);
  const [ownerName, setOwnerName] = React.useState<string>('');
  const [ownerPhone, setOwnerPhone] = React.useState<string>('');

  const [fields, setFields] = React.useState<ClinicField[]>([]);
  const [values, setValues] = React.useState<Record<string, any>>({});
  const [loading, setLoading] = React.useState<boolean>(false);
  const [submitting, setSubmitting] = React.useState<boolean>(false);
  const [error, setError] = React.useState<string | null>(null);
  const [refreshing, setRefreshing] = React.useState<boolean>(false);

  const [optionModal, setOptionModal] = React.useState<OptionModalState>({
    visible: false,
    fieldId: null,
    label: '',
    multiple: false,
    options: [],
    selected: [],
  });

  const [dateTimePicker, setDateTimePicker] = React.useState<DateTimePickerState>({
    visible: false,
    fieldId: null,
    mode: 'date',
    value: new Date(),
  });

  React.useEffect(() => {
    (async () => {
      try {
        // If we don't have a clinic ID from route params, try to get it from storage
        if (!routeClinicId) {
          const stored = await AsyncStorage.getItem('selectedClinic');
          const parsed = stored ? JSON.parse(stored) : null;
          setClinicId(parsed?.id ?? null);
        }
      } catch {}
      try {
        const me = await API.get('/me');
        setOwnerName(me.data?.name || '');
      } catch {}
    })();
  }, [routeClinicId]);
  
  // Handle date and time slot from calendar if provided
  React.useEffect(() => {
    if (calendarDate || calendarTimeSlot) {
      console.log('Received calendar selections:', { calendarDate, calendarTimeSlot });
      
      // Update values with calendar date if provided
      if (calendarDate) {
        setValues(prev => ({
          ...prev,
          appointment_date: calendarDate
        }));
      }
      
      // Update values with calendar time if provided
      if (calendarTimeSlot) {
        setValues(prev => ({
          ...prev,
          appointment_time: calendarTimeSlot.start
        }));
        
        // Show an alert to inform the user about the pre-selected time
        if (calendarDate) {
          // Format date for display
          const dateParts = calendarDate.split('-');
          const formattedDate = new Date(
            parseInt(dateParts[0]), 
            parseInt(dateParts[1]) - 1, 
            parseInt(dateParts[2])
          );
          
          Alert.alert(
            "Pre-selected Appointment",
            `You've selected ${format(formattedDate, 'MMMM d, yyyy')} at ${calendarTimeSlot.display_time}. Please complete the form to book your appointment.`,
            [{ text: "OK" }]
          );
        }
      }
      
      // Highlight the appointment form to draw attention to it
      // This makes it clear that they need to complete the form after selecting a date/time
      setTimeout(() => {
        console.log('Highlighting appointment form after calendar selection');
        
        // Scroll to the appointment form
        if (scrollViewRef.current) {
          // Scroll to the owner info section
          scrollViewRef.current.scrollTo({ y: 300, animated: true });
        }
        
        // Set a temporary highlight effect
        setError('Please complete the form below to confirm your appointment');
        setTimeout(() => {
          // Clear the message after a few seconds
          setError(null);
        }, 3000);
      }, 500);
    }
  }, [calendarDate, calendarTimeSlot]);

  const fetchFields = React.useCallback(async (id: number) => {
    try {
      setLoading(true);
      setError(null);
      console.log(`Fetching form fields for clinic ID: ${id}`);
      
      const res = await API.get(`/clinics/${id}/fields`);
      const data: ClinicField[] = res.data?.data ?? res.data ?? [];
      
      console.log(`Received ${data.length} form fields from the server`);
      
      // Sort required fields first
      const sortedData = [...data].sort((a, b) => {
        if (a.required && !b.required) return -1;
        if (!a.required && b.required) return 1;
        return 0;
      });
      
      setFields(Array.isArray(sortedData) ? sortedData : []);
      
      // initialize defaults only for missing keys
      setValues((prev) => {
        const next = { ...prev } as Record<string, any>;
        (sortedData || []).forEach((f) => {
          const key = `f_${f.id}`;
          if (next[key] === undefined) {
            next[key] = f.type === 'checkbox' ? [] : '';
          }
        });
        return next;
      });
    } catch (e: any) {
      console.error('Error fetching fields:', e);
      setError(e?.response?.data?.message || e?.message || 'Failed to load form fields');
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    if (clinicId != null) fetchFields(clinicId);
  }, [clinicId, fetchFields]);

  const onRefresh = React.useCallback(async () => {
    if (clinicId == null) return;
    try {
      setRefreshing(true);
      await fetchFields(clinicId);
    } finally {
      setRefreshing(false);
    }
  }, [clinicId, fetchFields]);

  const openOptionModal = (field: ClinicField) => {
    const key = `f_${field.id}`;
    const current = values[key];
    setOptionModal({
      visible: true,
      fieldId: field.id,
      label: field.label,
      multiple: field.type === 'checkbox',
      options: (field.options || []) as string[],
      selected: Array.isArray(current) ? current : (current ? [current] : []),
    });
  };

  const openDateTimePicker = (field: ClinicField) => {
    const key = `f_${field.id}`;
    const currentValue = values[key];
    let initialDate = new Date();
    
    if (currentValue) {
      try {
        if (field.type === 'date') {
          // Try to parse existing date value
          const [year, month, day] = currentValue.split('-').map(Number);
          if (!isNaN(year) && !isNaN(month) && !isNaN(day)) {
            initialDate = new Date(year, month - 1, day);
          }
        } else if (field.type === 'time') {
          // Try to parse existing time value
          const [hours, minutes] = currentValue.split(':').map(Number);
          if (!isNaN(hours) && !isNaN(minutes)) {
            initialDate.setHours(hours, minutes, 0);
          }
        }
      } catch (e) {
        console.error('Error parsing date/time:', e);
      }
    }

    setDateTimePicker({
      visible: true,
      fieldId: field.id,
      mode: field.type as 'date' | 'time',
      value: initialDate,
    });
  };

  const handleDateTimeChange = (event: any, selectedDate?: Date) => {
    const { fieldId, mode } = dateTimePicker;
    
    // On Android, dismissing the picker passes null for selectedDate
    if (!selectedDate) {
      if (Platform.OS === 'android') {
        setDateTimePicker(prev => ({ ...prev, visible: false }));
      }
      return;
    }

    let formattedValue = '';
    if (mode === 'date') {
      formattedValue = format(selectedDate, 'yyyy-MM-dd');
    } else {
      formattedValue = format(selectedDate, 'HH:mm');
    }

    if (fieldId) {
      const key = `f_${fieldId}`;
      setValues(prev => ({
        ...prev,
        [key]: formattedValue
      }));
    }

    // On iOS the picker stays open, on Android it closes immediately
    if (Platform.OS === 'android') {
      setDateTimePicker(prev => ({ ...prev, visible: false }));
    } else {
      setDateTimePicker(prev => ({ ...prev, value: selectedDate }));
    }
  };

  const commitOptionSelection = () => {
    if (optionModal.fieldId == null) return;
    const key = `f_${optionModal.fieldId}`;
    const value = optionModal.multiple ? optionModal.selected : (optionModal.selected[0] || '');
    setValues((prev) => ({ ...prev, [key]: value }));
    setOptionModal({ ...optionModal, visible: false });
  };

  const validate = (): string | null => {
    if (!ownerName.trim()) return 'Owner name is required';
    if (!ownerPhone.trim()) return 'Owner phone is required';
    for (const f of fields) {
      if (f.required) {
        const v = values[`f_${f.id}`];
        const isEmptyArray = Array.isArray(v) && v.length === 0;
        if (v === undefined || v === '' || isEmptyArray) {
          return `${f.label} is required`;
        }
      }
    }
    return null;
  };

  const handleSubmit = async () => {
    const problem = validate();
    if (problem) {
      setError(problem);
      return;
    }
    if (clinicId == null) return;

    try {
      setSubmitting(true);
      setError(null);
      
      // Get the selected date and time - prioritize values from state which may include calendar selections
      const selectedDate = values.appointment_date || new Date().toISOString().split('T')[0];
      const selectedTime = values.appointment_time || '10:00';
      
      console.log('Submitting appointment with date:', selectedDate, 'and time:', selectedTime);
      
      const payload = {
        owner_name: ownerName.trim(),
        owner_phone: ownerPhone.trim(),
        appointment_date: selectedDate,
        appointment_time: selectedTime,
        responses: fields.map((f) => ({ field_id: f.id, value: values[`f_${f.id}`] })),
      };
      
      // Send the appointment request
      const res = await API.post(`/clinics/${clinicId}/appointments`, payload);
      
      // Force a refresh for this date in the calendar screen for all users
      try {
        // Mark this date/time as booked in local storage
        const bookedKey = `booked_${clinicId}_${selectedDate}_${selectedTime}`;
        await AsyncStorage.setItem(bookedKey, 'true');
        
        // Set refresh markers to force calendar to refresh
        const refreshKey = `refresh_calendar_${clinicId}_${selectedDate}`;
        await AsyncStorage.setItem(refreshKey, new Date().toISOString());
        
        console.log('Set refresh marker for calendar to refresh booked slots');
      } catch (e) {
        console.log('Error setting refresh markers', e);
      }
      
      // Show success alert
      Alert.alert(
        "Booking Successful",
        "Your appointment has been booked successfully!",
        [
          { 
            text: "OK", 
            onPress: () => {
              // If we came from the calendar, navigate back
              if (calendarDate || calendarTimeSlot) {
                // Clear any cached data for this date before navigating back
                try {
                  AsyncStorage.removeItem(`slots_cache_${clinicId}_${selectedDate}`);
                } catch (e) {
                  console.log('Error clearing cache', e);
                }
                navigation?.goBack();
              } else {
                // Otherwise just clear the form
                setValues((prev) => {
                  const cleared: Record<string, any> = { ...prev };
                  cleared.appointment_date = '';
                  cleared.appointment_time = '';
                  fields.forEach((f) => {
                    const key = `f_${f.id}`;
                    cleared[key] = f.type === 'checkbox' ? [] : '';
                  });
                  return cleared;
                });
                
                // Show success message in the banner
                setError('Appointment booked successfully!');
              }
            }
          }
        ]
      );
    } catch (e: any) {
      setError(e?.response?.data?.message || e?.message || 'Failed to submit appointment');
    } finally {
      setSubmitting(false);
    }
  };

  const renderField = (f: ClinicField) => {
    const key = `f_${f.id}`;
    const val = values[key];

    if (f.type === 'textarea') {
      return (
        <View key={key} style={styles.inputGroup}>
          <Text style={styles.label}>{f.label}{f.required ? ' *' : ''}</Text>
          <TextInput
            style={[styles.input, styles.textarea]}
            multiline
            numberOfLines={4}
            value={val}
            onChangeText={(t) => setValues((p) => ({ ...p, [key]: t }))}
            placeholder={`Enter ${f.label.toLowerCase()}`}
            placeholderTextColor="#888"
          />
        </View>
      );
    }

    if (f.type === 'select' || f.type === 'radio' || f.type === 'checkbox') {
      const display = Array.isArray(val) ? (val.length ? `${val.length} selected` : 'Select') : (val || 'Select');
      return (
        <View key={key} style={styles.inputGroup}>
          <Text style={styles.label}>{f.label}{f.required ? ' *' : ''}</Text>
          <TouchableOpacity style={styles.selector} onPress={() => openOptionModal(f)}>
            <Text style={styles.selectorText}>{display}</Text>
            <Ionicons name="chevron-down" size={18} color="#666" />
          </TouchableOpacity>
        </View>
      );
    }

    // Date picker
    if (f.type === 'date') {
      let displayValue = val || 'Select date';
      if (val) {
        try {
          // If it's already in YYYY-MM-DD format, make it more readable
          const [year, month, day] = val.split('-');
          if (year && month && day) {
            const date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
            displayValue = format(date, 'MMMM d, yyyy');
          }
        } catch (e) {
          console.error('Error formatting date:', e);
        }
      }

      return (
        <View key={key} style={styles.inputGroup}>
          <Text style={styles.label}>{f.label}{f.required ? ' *' : ''}</Text>
          <TouchableOpacity 
            style={styles.selector} 
            onPress={() => openDateTimePicker(f)}
          >
            <Text style={styles.selectorText}>{displayValue}</Text>
            <Ionicons name="calendar" size={18} color="#666" />
          </TouchableOpacity>
        </View>
      );
    }

    // Time picker
    if (f.type === 'time') {
      let displayValue = val || 'Select time';
      if (val) {
        try {
          // If it's in HH:MM format, make it more readable
          const [hours, minutes] = val.split(':');
          if (hours && minutes) {
            const date = new Date();
            date.setHours(parseInt(hours), parseInt(minutes), 0);
            displayValue = format(date, 'h:mm a'); // 12-hour format with AM/PM
          }
        } catch (e) {
          console.error('Error formatting time:', e);
        }
      }

      return (
        <View key={key} style={styles.inputGroup}>
          <Text style={styles.label}>{f.label}{f.required ? ' *' : ''}</Text>
          <TouchableOpacity 
            style={styles.selector} 
            onPress={() => openDateTimePicker(f)}
          >
            <Text style={styles.selectorText}>{displayValue}</Text>
            <Ionicons name="time" size={18} color="#666" />
          </TouchableOpacity>
        </View>
      );
    }

    // number/text
    const keyboardType = f.type === 'number' ? 'numeric' : 'default';
    return (
      <View key={key} style={styles.inputGroup}>
        <Text style={styles.label}>{f.label}{f.required ? ' *' : ''}</Text>
        <TextInput
          style={styles.input}
          value={String(val ?? '')}
          onChangeText={(t) => setValues((p) => ({ ...p, [key]: t }))}
          placeholder={`Enter ${f.label.toLowerCase()}`}
          placeholderTextColor="#888"
          keyboardType={keyboardType as any}
        />
      </View>
    );
  };

  return (
    <ScrollView 
      ref={scrollViewRef}
      style={styles.bg} 
      contentContainerStyle={styles.content} 
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={PINK} />}>
      <Text style={styles.header}>Book Appointment</Text>

      {/* Calendar selection info */}
      {(calendarDate || calendarTimeSlot) && (
        <View style={[styles.banner, styles.bannerCalendar]}>
          <View style={styles.calendarBannerContent}>
            <Ionicons name="calendar-outline" size={24} color={PURPLE} />
            <View style={styles.calendarBannerDetails}>
              <Text style={styles.calendarBannerTitle}>Selected Appointment Time</Text>
              <Text style={styles.calendarBannerText}>
                {calendarDate ? `Date: ${format(new Date(calendarDate), 'MMMM d, yyyy')}` : ''}
              </Text>
              {calendarTimeSlot && (
                <Text style={styles.calendarBannerText}>
                  Time: {calendarTimeSlot.display_time}
                </Text>
              )}
              <Text style={styles.calendarBannerNote}>
                This time slot is reserved for you while completing this form
              </Text>
              <TouchableOpacity 
                style={styles.changeDateButton}
                onPress={() => navigation?.navigate('ClinicCalendar')}
              >
                <Text style={styles.changeDateButtonText}>Change Date/Time</Text>
                <Ionicons name="calendar" size={16} color={PURPLE} />
              </TouchableOpacity>
            </View>
          </View>
        </View>
      )}

      {/* Error / status banner */}
      {error && (
        <View style={[styles.banner, error.includes('successfully') ? styles.bannerSuccess : styles.bannerError]}>
          <Text style={styles.bannerText}>{error}</Text>
        </View>
      )}

      {/* Owner info */}
      <View style={styles.card}>
        <View style={styles.cardHeader}>
          <Ionicons name="person-circle-outline" size={26} color={PURPLE} />
          <Text style={styles.cardTitle}>Owner Information</Text>
        </View>
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Owner Name *</Text>
          <TextInput style={styles.input} value={ownerName} onChangeText={setOwnerName} placeholder="Enter your name" placeholderTextColor="#888" />
        </View>
        <View style={styles.inputGroup}>
          <Text style={styles.label}>Owner Phone *</Text>
          <TextInput style={styles.input} value={ownerPhone} onChangeText={setOwnerPhone} placeholder="e.g. 09xxxxxxxxx" placeholderTextColor="#888" keyboardType="phone-pad" />
        </View>
      </View>

      {/* Dynamic fields */}
      <View style={styles.card}>
        <View style={styles.cardHeader}>
          <Ionicons name="clipboard-outline" size={24} color={PURPLE} />
          <Text style={styles.cardTitle}>Appointment Details</Text>
        </View>
        {loading ? (
          <View style={{ paddingVertical: 16 }}>
            <ActivityIndicator color={PINK} />
          </View>
        ) : (
          <View>
            {fields.map(renderField)}
            {fields.length === 0 && (
              <Text style={{ color: '#666', fontSize: 14 }}>No fields configured for this clinic.</Text>
            )}
          </View>
        )}
      </View>

      <TouchableOpacity style={styles.submitButton} onPress={handleSubmit} disabled={submitting || loading || !clinicId}>
        {submitting ? <ActivityIndicator color={DARK} /> : <Text style={styles.submitText}>Book Appointment</Text>}
      </TouchableOpacity>

      {/* Options Modal */}
      <Modal visible={optionModal.visible} transparent animationType="fade" onRequestClose={() => setOptionModal((s) => ({ ...s, visible: false }))}>
        <View style={styles.modalOverlay}>
          <View style={styles.modalCard}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>{optionModal.label}</Text>
              <TouchableOpacity onPress={() => setOptionModal((s) => ({ ...s, visible: false }))}>
                <Ionicons name="close" size={22} color={DARK} />
              </TouchableOpacity>
            </View>
            <FlatList
              data={optionModal.options}
              keyExtractor={(opt) => opt}
              renderItem={({ item }) => {
                const selected = optionModal.selected.includes(item);
                return (
                  <TouchableOpacity
                    style={[styles.optionRow, selected && styles.optionRowSelected]}
                    onPress={() => {
                      setOptionModal((s) => {
                        if (s.multiple) {
                          const exists = s.selected.includes(item);
                          const next = exists ? s.selected.filter((x) => x !== item) : [...s.selected, item];
                          return { ...s, selected: next };
                        }
                        return { ...s, selected: [item] };
                      });
                    }}
                  >
                    <Text style={[styles.optionText, selected && styles.optionTextSelected]}>{item}</Text>
                    {selected && <Ionicons name="checkmark" size={18} color={PINK} />}
                  </TouchableOpacity>
                );
              }}
            />
            <TouchableOpacity style={styles.modalSave} onPress={commitOptionSelection}>
              <Text style={styles.modalSaveText}>Done</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>

      {/* DateTimePicker for iOS and Android */}
      {dateTimePicker.visible && (
        <>
          {Platform.OS === 'ios' && (
            <Modal visible={true} transparent animationType="slide">
              <View style={styles.modalOverlay}>
                <View style={[styles.modalCard, { padding: 0 }]}>
                  <View style={[styles.modalHeader, { padding: 14 }]}>
                    <Text style={styles.modalTitle}>
                      Select {dateTimePicker.mode === 'date' ? 'Date' : 'Time'}
                    </Text>
                    <TouchableOpacity onPress={() => setDateTimePicker(prev => ({ ...prev, visible: false }))}>
                      <Ionicons name="close" size={22} color={DARK} />
                    </TouchableOpacity>
                  </View>
                  
                  <DateTimePicker
                    value={dateTimePicker.value}
                    mode={dateTimePicker.mode}
                    display="spinner"
                    onChange={handleDateTimeChange}
                    style={{ width: '100%', height: 200 }}
                  />
                  
                  <TouchableOpacity 
                    style={styles.modalSave}
                    onPress={() => {
                      // For iOS we need to manually close the modal and apply the date
                      handleDateTimeChange({ type: 'set' }, dateTimePicker.value);
                      setDateTimePicker(prev => ({ ...prev, visible: false }));
                    }}
                  >
                    <Text style={styles.modalSaveText}>Done</Text>
                  </TouchableOpacity>
                </View>
              </View>
            </Modal>
          )}
          
          {/* For Android, the picker appears as a dialog automatically */}
          {Platform.OS === 'android' && (
            <DateTimePicker
              value={dateTimePicker.value}
              mode={dateTimePicker.mode}
              is24Hour={true}
              onChange={handleDateTimeChange}
            />
          )}
        </>
      )}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  bg: { flex: 1, backgroundColor: LIGHT },
  content: { padding: 22, paddingBottom: 40 },
  header: { fontSize: 24, fontWeight: 'bold', color: DARK, marginTop: 10, marginBottom: 16, textAlign: 'center' },

  banner: { padding: 12, borderRadius: 10, marginBottom: 12 },
  bannerError: { backgroundColor: '#fdecea', borderColor: '#f5c2c0', borderWidth: 1 },
  bannerSuccess: { backgroundColor: '#e7f6ed', borderColor: '#b7e1c7', borderWidth: 1 },
  bannerCalendar: { backgroundColor: '#f1ebff', borderColor: PURPLE, borderWidth: 1, padding: 16 },
  bannerText: { color: DARK, textAlign: 'center', fontSize: 14 },
  calendarBannerContent: { flexDirection: 'row', alignItems: 'flex-start' },
  calendarBannerDetails: { marginLeft: 12, flex: 1 },
  calendarBannerTitle: { fontWeight: 'bold', fontSize: 16, color: DARK, marginBottom: 4 },
  calendarBannerText: { color: DARK, fontSize: 14, marginBottom: 2 },
  calendarBannerNote: { color: '#666', fontSize: 12, fontStyle: 'italic', marginTop: 4 },
  changeDateButton: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', marginTop: 10, padding: 8, backgroundColor: '#f5f0ff', borderRadius: 8, borderWidth: 1, borderColor: PURPLE },
  changeDateButtonText: { color: PURPLE, marginRight: 8, fontSize: 13, fontWeight: '500' },

  card: { backgroundColor: WHITE, borderRadius: 18, padding: 18, marginBottom: 15, shadowColor: '#B39DDB', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.07, shadowRadius: 12, elevation: 3 },
  cardHeader: { flexDirection: 'row', alignItems: 'center', marginBottom: 10 },
  cardTitle: { fontSize: 17, fontWeight: 'bold', color: PURPLE, marginLeft: 10 },

  inputGroup: { marginBottom: 12 },
  label: { fontSize: 14, color: DARK, marginBottom: 6 },
  input: { backgroundColor: '#fff', borderRadius: 12, borderWidth: 1, borderColor: '#eee', paddingHorizontal: 14, paddingVertical: 12, fontSize: 15 },
  textarea: { minHeight: 100, textAlignVertical: 'top' },

  selector: { backgroundColor: '#fff', borderRadius: 12, borderWidth: 1, borderColor: '#eee', paddingHorizontal: 14, paddingVertical: 14, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  selectorText: { fontSize: 15, color: DARK },

  submitButton: { backgroundColor: PINK, paddingVertical: 14, borderRadius: 14, alignItems: 'center', justifyContent: 'center', marginTop: 4 },
  submitText: { color: DARK, fontSize: 16, fontWeight: 'bold' },

  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.35)', justifyContent: 'center', alignItems: 'center', padding: 16 },
  modalCard: { backgroundColor: WHITE, width: '92%', maxHeight: '70%', borderRadius: 14, padding: 14 },
  modalHeader: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 8 },
  modalTitle: { fontSize: 16, fontWeight: 'bold', color: DARK },
  optionRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingVertical: 12, borderBottomWidth: 1, borderBottomColor: '#f2f2f2' },
  optionRowSelected: { backgroundColor: '#faf6ff' },
  optionText: { fontSize: 15, color: DARK },
  optionTextSelected: { color: PURPLE, fontWeight: 'bold' },
  modalSave: { backgroundColor: PINK, paddingVertical: 12, alignItems: 'center', borderRadius: 10, marginTop: 10 },
  modalSaveText: { color: DARK, fontWeight: 'bold' },
});